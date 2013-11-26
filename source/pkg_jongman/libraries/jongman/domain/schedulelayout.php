<?php
defined('_JEXEC') or die;

jimport('jongman.domain.value.dayofweek');
jimport('jongman.domain.scheduleperiod');


interface ILayoutTimezone
{
	public function timezone();
}

interface IDailyScheduleLayout
{
	/**
	 * @return bool
	 */
	public function usesDailyLayouts();
}

interface IScheduleLayout extends ILayoutTimezone, IDailyScheduleLayout
{
	/**
	 * @param Date $layoutDate
	 * @param bool $hideBlockedPeriods
	 * @return SchedulePeriod[]|array of SchedulePeriod objects
	 */
	public function getLayout(JMDate $layoutDate, $hideBlockedPeriods = false);

	/**
	 * @abstract
	 * @param Date $date
	 * @return SchedulePeriod|null period which occurs at this datetime. Includes start time, excludes end time. null if no match is found
	 */
	public function getPeriod(JMDate $date);
}

interface ILayoutCreation extends ILayoutTimezone, IDailyScheduleLayout
{
	/**
	 * Appends a period to the schedule layout
	 *
	 * @param Time $startTime starting time of the schedule in specified timezone
	 * @param Time $endTime ending time of the schedule in specified timezone
	 * @param string $label optional label for the period
	 * @param DayOfWeek|int|null $dayOfWeek
	 */
	function appendPeriod(Time $startTime, Time $endTime, $label = null, $dayOfWeek = null);

	/**
	 * Appends a period that is not reservable to the schedule layout
	 *
	 * @param Time $startTime starting time of the schedule in specified timezone
	 * @param Time $endTime ending time of the schedule in specified timezone
	 * @param string $label optional label for the period
	 * @param DayOfWeek|int|null $dayOfWeek
	 * @return void
	 */
	function appendBlockedPeriod(Time $startTime, Time $endTime, $label = null, $dayOfWeek = null);

	/**
	 *
	 * @param DayOfWeek|int|null $dayOfWeek
	 * @return LayoutPeriod[] array of LayoutPeriod
	 */
	function getSlots($dayOfWeek = null);
}

class ScheduleLayout implements IScheduleLayout, ILayoutCreation
{
	/**
	 * @var array|LayoutPeriod[]
	 */
	private $_periods = array();

	/**
	 * @var string
	 */
	private $targetTimezone;

	/**
	 * @var bool
	 */
	private $cached = false;

	private $cachedPeriods = array();

	/**
	 * @var bool
	 */
	private $usingDailyLayouts = false;

	/**
	 * @var string
	 */
	private $layoutTimezone;

	/**
	 * @param string $targetTimezone target timezone of layout
	 */
	public function __construct($targetTimezone = null)
	{
		$this->targetTimezone = $targetTimezone;
		if ($targetTimezone == null)
		{
			$this->targetTimezone = JFactory::getConfig()->get('offset');
		}
	}

	/**
	 * @param DayOfWeek|int|null $dayOfWeek
	 * @throws Exception
	 * @return LayoutPeriod[]|array
	 */
	public function getSlots($dayOfWeek = null)
	{
		if (is_null($dayOfWeek))
		{
			if ($this->usingDailyLayouts)
			{
				throw new Exception('ScheduleLayout->GetSlots() $dayOfWeek required when using daily layouts');
			}
			$periods = $this->_periods;
		}
		else
		{
			if (!$this->usingDailyLayouts)
			{
				throw new Exception('ScheduleLayout->GetSlots() $dayOfWeek cannot be provided when using single layout');
			}
			$periods = $this->_periods[$dayOfWeek];
		}
		$this->sortItems($periods);
		return $periods;
	}

	/**
	 * Appends a period to the schedule layout
	 *
	 * @param Time $startTime starting time of the schedule in specified timezone
	 * @param Time $endTime ending time of the schedule in specified timezone
	 * @param string $label optional label for the period
	 * @param DayOfWeek|int|null $dayOfWeek
	 */
	public function appendPeriod(Time $startTime, Time $endTime, $label = null, $dayOfWeek = null)
	{
		$this->appendGenericPeriod($startTime, $endTime, PeriodTypes::RESERVABLE, $label, $dayOfWeek);
	}

	/**
	 * Appends a period that is not reservable to the schedule layout
	 *
	 * @param Time $startTime starting time of the schedule in specified timezone
	 * @param Time $endTime ending time of the schedule in specified timezone
	 * @param string $label optional label for the period
	 * @param DayOfWeek|int|null $dayOfWeek
	 * @return void
	 */
	public function appendBlockedPeriod(Time $startTime, Time $endTime, $label = null, $dayOfWeek = null)
	{
		$this->appendGenericPeriod($startTime, $endTime, PeriodTypes::NONRESERVABLE, $label, $dayOfWeek);
	}

	protected function appendGenericPeriod(Time $startTime, Time $endTime, $periodType, $label = null,
										   $dayOfWeek = null)
	{
		$this->layoutTimezone = $startTime->timezone();
		$layoutPeriod = new LayoutPeriod($startTime, $endTime, $periodType, $label);
		if (!is_null($dayOfWeek))
		{
			$this->usingDailyLayouts = true;
			$this->_periods[$dayOfWeek][] = $layoutPeriod;
		}
		else
		{
			$this->_periods[] = $layoutPeriod;
		}
	}

	/**
	 * @param Date $start
	 * @param Date $end
	 * @return bool
	 */
	protected function spansMidnight(JMDate $start, JMDate $end)
	{
		return !$start->dateEquals($end) && !$end->isMidnight();
	}

	/**
	 * @param Date $layoutDate
	 * @param bool $hideBlockedPeriods
	 * @return array|SchedulePeriod[]
	 */
	public function getLayout(JMDate $layoutDate, $hideBlockedPeriods = false)
	{
		if ($this->usingDailyLayouts)
		{
			return $this->getLayoutDaily($layoutDate, $hideBlockedPeriods);
		}
		$targetTimezone = $this->targetTimezone;
		$layoutDate = $layoutDate->toTimezone($targetTimezone);

		$cachedValues = $this->getCachedValuesForDate($layoutDate);
		if (!empty($cachedValues))
		{
			return $cachedValues;
		}

		$list = new PeriodList();

		$periods = $this->getPeriods($layoutDate);

		$layoutTimezone = $periods[0]->timezone();
		$workingDate = JMDate::Create($layoutDate->year(), $layoutDate->month(), $layoutDate->day(), 0, 0, 0,
									$layoutTimezone);
		$midnight = $layoutDate->getDate();

		/* @var $period LayoutPeriod */
		foreach ($periods as $period)
		{
			if ($hideBlockedPeriods && !$period->isReservable())
			{
				continue;
			}
			$start = $period->start;
			$end = $period->end;
			$periodType = $period->periodTypeClass();
			$label = $period->label;
			$labelEnd = null;

			// convert to target timezone
			$periodStart = $workingDate->setTime($start)->toTimezone($targetTimezone);
			$periodEnd = $workingDate->setTime($end, true)->toTimezone($targetTimezone);

			if ($periodEnd->lessThan($periodStart))
			{
				$periodEnd = $periodEnd->addDays(1);
			}

			$startTime = $periodStart->getTime();
			$endTime = $periodEnd->getTime();

			if ($this->bothDatesAreOff($periodStart, $periodEnd, $layoutDate))
			{
				$periodStart = $layoutDate->setTime($startTime);
				$periodEnd = $layoutDate->setTime($endTime, true);
			}

			if ($this->spansMidnight($periodStart, $periodEnd, $layoutDate))
			{
				if ($periodStart->lessThan($midnight))
				{
					// add compensating period at end
					$start = $layoutDate->setTime($startTime);
					$end = $periodEnd->addDays(1);
					$list->Add($this->buildPeriod($periodType, $start, $end, $label, $labelEnd));
				}
				else
				{
					// add compensating period at start
					$start = $periodStart->addDays(-1);
					$end = $layoutDate->setTime($endTime, true);
					$list->add($this->buildPeriod($periodType, $start, $end, $label, $labelEnd));
				}
			}

			if (!$periodStart->isMidnight() && $periodStart->lessThan($layoutDate) && $periodEnd->dateEquals($layoutDate) && $periodEnd->isMidnight())
			{
				$periodStart = $periodStart->addDays(1);
				$periodEnd = $periodEnd->addDays(1);
			}

			$list->Add($this->buildPeriod($periodType, $periodStart, $periodEnd, $label, $labelEnd));
		}

		$layout = $list->GetItems();
		$this->sortItems($layout);
		$this->addCached($layout, $workingDate);

		return $layout;
	}

	private function getLayoutDaily(JMDate $requestedDate, $hideBlockedPeriods = false)
	{
		if ($requestedDate->Timezone() != $this->targetTimezone)
		{
			throw new Exception('Target timezone and requested timezone do not match');
		}

		$cachedValues = $this->gtCachedValuesForDate($requestedDate);
		if (!empty($cachedValues))
		{
			return $cachedValues;
		}

		// check cache
		$baseDateInLayoutTz = JMDate::create($requestedDate->year(), $requestedDate->month(), $requestedDate->day(),
										   0, 0, 0, $this->layoutTimezone);


		$list = new PeriodList();
		$this->addDailyPeriods($requestedDate->weekday(), $baseDateInLayoutTz, $requestedDate, $list, $hideBlockedPeriods);

		if ($this->layoutTimezone != $this->targetTimezone)
		{
			$requestedDateInTargetTz = $requestedDate->toTimezone($this->layoutTimezone);

			$adjustment = 0;
			if ($requestedDateInTargetTz->format('YmdH') < $requestedDate->format('YmdH'))
			{
				$adjustment = -1;
			}
			else
			{
				if ($requestedDateInTargetTz->format('YmdH') > $requestedDate->format('YmdH'))
				{
					$adjustment = 1;
				}
			}

			if ($adjustment != 0)
			{
				$adjustedDate = $requestedDate->addDays($adjustment);
				$baseDateInLayoutTz = $baseDateInLayoutTz->addDays($adjustment);
				$this->AddDailyPeriods($adjustedDate->weekday(), $baseDateInLayoutTz, $requestedDate, $list);
			}
		}
		$layout = $list->getItems();
		$this->sortItems($layout);
		$this->addCached($layout, $requestedDate);
		return $layout;
	}

	/**
	 * @param int $day
	 * @param Date $baseDateInLayoutTz
	 * @param Date $requestedDate
	 * @param PeriodList $list
	 * @param bool $hideBlockedPeriods
	 */
	private function addDailyPeriods($day, $baseDateInLayoutTz, $requestedDate, $list, $hideBlockedPeriods = false)
	{
		$periods = $this->_periods[$day];
		/** @var $period LayoutPeriod */
		foreach ($periods as $period)
		{
			if ($hideBlockedPeriods && !$period->isReservable())
			{
				continue;
			}
			$begin = $baseDateInLayoutTz->SetTime($period->Start)->toTimezone($this->targetTimezone);
			$end = $baseDateInLayoutTz->SetTime($period->End, true)->toTimezone($this->targetTimezone);
			// only add this period if it occurs on the requested date
			if ($begin->dateEquals($requestedDate) || ($end->dateEquals($requestedDate) && !$end->isMidnight()))
			{
				$built = $this->buildPeriod($period->periodTypeClass(), $begin, $end, $period->label);
				$list->add($built);
			}
		}
	}

	/**
	 * @param array|SchedulePeriod[] $layout
	 * @param Date $date
	 */
	private function addCached($layout, $date)
	{
		$this->cached = true;
		$this->cachedPeriods[$date->format('Ymd')] = $layout;
	}

	/**
	 * @param Date $date
	 * @return array|SchedulePeriod[]
	 */
	private function getCachedValuesForDate($date)
	{
		$key = $date->format('Ymd');
		if (array_key_exists($date->format('Ymd'), $this->cachedPeriods))
		{
			return $this->cachedPeriods[$key];
		}
		return null;
	}

	private function bothDatesAreOff(JMDate $start, JMDate $end, JMDate $layoutDate)
	{
		return !$start->dateEquals($layoutDate) && !$end->dateEquals($layoutDate);
	}

	private function buildPeriod($periodType, JMDate $start, JMDate $end, $label, $labelEnd = null)
	{
		return new $periodType($start, $end, $label, $labelEnd);
	}

	protected function SortItems(&$items)
	{
		usort($items, array("ScheduleLayout", "SortBeginTimes"));
	}

	public function Timezone()
	{
		return $this->targetTimezone;
	}

	protected function addPeriod(SchedulePeriod $period)
	{
		$this->_periods[] = $period;
	}

	/**
	 * @static
	 * @param SchedulePeriod|LayoutPeriod $period1
	 * @param SchedulePeriod|LayoutPeriod $period2
	 * @return int
	 */
	static function sortBeginTimes($period1, $period2)
	{
		return $period1->Compare($period2);
	}

	/**
	 * @param string $timezone
	 * @param string $reservableSlots
	 * @param string $blockedSlots
	 * @return ScheduleLayout
	 */
	public static function parse($timezone, $reservableSlots, $blockedSlots)
	{
		$parser = new LayoutParser($timezone);
		$parser->addReservable($reservableSlots);
		$parser->addBlocked($blockedSlots);
		return $parser->getLayout();
	}

	/**
	 * @param string $timezone
	 * @param string[]|array $reservableSlots
	 * @param string[]|array $blockedSlots
	 * @throws Exception
	 * @return ScheduleLayout
	 */
	public static function parseDaily($timezone, $reservableSlots, $blockedSlots)
	{
		if (count($reservableSlots) != DayOfWeek::NumberOfDays || count($blockedSlots) != DayOfWeek::NumberOfDays)
		{
			throw new Exception(sprintf('LayoutParser ParseDaily missing slots. $reservableSlots=%s, $blockedSlots=%s',
										count($reservableSlots), count($blockedSlots)));
		}
		$parser = new LayoutParser($timezone);

		foreach (DayOfWeek::Days() as $day)
		{
			$parser->AddReservable($reservableSlots[$day], $day);
			$parser->AddBlocked($blockedSlots[$day], $day);
		}

		return $parser->GetLayout();
	}

	/**
	 * @param Date $date
	 * @return SchedulePeriod period which occurs at this datetime. Includes start time, excludes end time
	 */
	public function getPeriod(JMDate $date)
	{
		$timezone = $this->layoutTimezone;
		$tempDate = $date->toTimezone($timezone);
		$periods = $this->getPeriods($tempDate);

		/** @var $period LayoutPeriod */
		foreach ($periods as $period)
		{
			$start = JMDate::create($tempDate->year(), $tempDate->month(), $tempDate->day(), $period->start->hour(),
								  $period->start->Minute(), 0, $timezone);
			$end = JMDate::create($tempDate->year(), $tempDate->month(), $tempDate->day(), $period->end->hour(),
								$period->end->minute(), 0, $timezone);

			if ($end->lessThan($start) || $end->isMidnight())
			{
				$end = $end->addDays(1);
			}

			if ($start->compare($date) <= 0 && $end->compare($date) > 0)
			{
				return $this->buildPeriod($period->periodTypeClass(), $start, $end, $period->label);
			}
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function usesDailyLayouts()
	{
		return $this->usingDailyLayouts;
	}

	private function getPeriods(JMDate $layoutDate)
	{
		if ($this->usingDailyLayouts)
		{
			$dayOfWeek = $layoutDate->weekday();
			return $this->_periods[$dayOfWeek];
		}
		else
		{
			return $this->_periods;
		}
	}
}

class LayoutParser
{
	private $layout;
	private $timezone;

	public function __construct($timezone)
	{
		$this->layout = new ScheduleLayout($timezone);
		$this->timezone = $timezone;
	}

	public function addReservable($reservableSlots, $dayOfWeek = null)
	{
		$cb = array($this, 'appendPeriod');
		$this->parseSlots($reservableSlots, $dayOfWeek, $cb);
	}

	public function addBlocked($blockedSlots, $dayOfWeek = null)
	{
		$cb = array($this, 'appendBlocked');

		$this->parseSlots($blockedSlots, $dayOfWeek, $cb);
	}

	public function getLayout()
	{
		return $this->layout;
	}

	private function appendPeriod($start, $end, $label, $dayOfWeek = null)
	{
		$this->layout->AppendPeriod(Time::Parse($start, $this->timezone),
									Time::Parse($end, $this->timezone),
									$label,
									$dayOfWeek);
	}

	private function appendBlocked($start, $end, $label, $dayOfWeek = null)
	{
		$this->layout->AppendBlockedPeriod(Time::Parse($start, $this->timezone),
										   Time::Parse($end, $this->timezone),
										   $label,
										   $dayOfWeek);
	}

	private function parseSlots($allSlots, $dayOfWeek, $callback)
	{
		$lines = preg_split("/[\n]/", $allSlots, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($lines as $slotLine)
		{
			$label = null;
			$parts = preg_split('/(\d?\d:\d\d\s*\-\s*\d?\d:\d\d)(.*)/', trim($slotLine), -1,
								PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			$times = explode('-', $parts[0]);
			$start = trim($times[0]);
			$end = trim($times[1]);

			if (count($parts) > 1)
			{
				$label = trim($parts[1]);
			}

			call_user_func($callback, $start, $end, $label, $dayOfWeek);
		}
	}
}

class LayoutPeriod
{
	/**
	 * @var Time
	 */
	public $start;

	/**
	 * @var Time
	 */
	public $end;

	/**
	 * @var PeriodTypes
	 */
	public $periodType;

	/**
	 * @var string
	 */
	public $label;

	/**
	 * @return string
	 */
	public function periodTypeClass()
	{
		if ($this->periodType == PeriodTypes::RESERVABLE)
		{
			return 'SchedulePeriod';
		}

		return 'NonSchedulePeriod';
	}

	/**
	 * @return bool
	 */
	public function isReservable()
	{
		return $this->periodType == PeriodTypes::RESERVABLE;
	}

	/**
	 * @return bool
	 */
	public function isLabelled()
	{
		return !empty($this->label);
	}

	/**
	 * @return string
	 */
	public function timezone()
	{
		return $this->start->timezone();
	}

	public function __construct(Time $start, Time $end, $periodType = PeriodTypes::RESERVABLE, $label = null)
	{
		$this->start = $start;
		$this->end = $end;
		$this->periodType = $periodType;
		$this->label = $label;
	}

	/**
	 * Compares the starting times
	 */
	public function compare(LayoutPeriod $other)
	{
		return $this->start->compare($other->start);
	}
}

class PeriodList
{
	private $items = array();
	private $_addedStarts = array();
	private $_addedTimes = array();
	private $_addedEnds = array();

	public function add(SchedulePeriod $period)
	{
		if (!$period->IsReservable())
		{
			//TODO: Config option to hide non-reservable periods
		}

		if ($this->alreadyAdded($period->beginDate(), $period->endDate()))
		{
			//echo "already added $period\n";
			return;
		}

		//echo "\nadding {$period->BeginDate()} - {$period->EndDate()}";
		$this->items[] = $period;
	}

	public function getItems()
	{
		return $this->items;
	}

	private function alreadyAdded(JMDate $start, JMDate $end)
	{
		$startExists = false;
		$endExists = false;

		if (array_key_exists($start->timestamp(), $this->_addedStarts))
		{
			$startExists = true;
		}

		if (array_key_exists($end->timestamp(), $this->_addedEnds))
		{
			$endExists = true;
		}

		$this->_addedTimes[$start->timestamp()] = true;
		$this->_addedEnds[$end->timestamp()] = true;

		return $startExists || $endExists;
	}
}

class ReservationLayout extends ScheduleLayout implements IScheduleLayout
{
	protected function spansMidnight(JMDate $start, JMDate $end)
	{
		return false;
	}
}

?>