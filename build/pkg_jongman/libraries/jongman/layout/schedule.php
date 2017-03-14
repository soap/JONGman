<?php
defined('_JEXEC') or die;

class RFLayoutSchedule implements ILayoutSchedule, ILayoutCreation
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
	public function appendPeriod(RFTime $startTime, RFTime $endTime, $label = null, $dayOfWeek = null)
	{
		$this->appendGenericPeriod($startTime, $endTime, RFSchedulePeriodTypes::RESERVABLE, $label, $dayOfWeek);
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
	public function appendBlockedPeriod(RFTime $startTime, RFTime $endTime, $label = null, $dayOfWeek = null)
	{
		$this->appendGenericPeriod($startTime, $endTime, RFSchedulePeriodTypes::NONRESERVABLE, $label, $dayOfWeek);
	}

	protected function appendGenericPeriod(RFTime $startTime, RFTime $endTime, $periodType, $label = null,
										   $dayOfWeek = null)
	{
		$this->layoutTimezone = $startTime->timezone();
		$layoutPeriod = new RFLayoutPeriod($startTime, $endTime, $periodType, $label);
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
	protected function spansMidnight(RFDate $start, RFDate $end)
	{
		return !$start->dateEquals($end) && !$end->isMidnight();
	}

	/**
	 * Get schedule layout after having completed appending periods 
	 * @param Date $layoutDate	Layout for spefied date, you may use different layout for each day of week
	 * @param bool $hideBlockedPeriods Get blocked period (unreservable) or not
	 * @return array|SchedulePeriod[]
	 */
	public function getLayout(RFDate $layoutDate, $hideBlockedPeriods = false)
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

		$list = new RFSchedulePeriodList();

		$periods = $this->getPeriods($layoutDate);

		$layoutTimezone = $periods[0]->timezone();
		$workingDate = RFDate::Create($layoutDate->year(), $layoutDate->month(), $layoutDate->day(), 0, 0, 0,
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
					$list->add($this->buildPeriod($periodType, $start, $end, $label, $labelEnd));
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

	private function getLayoutDaily(RFDate $requestedDate, $hideBlockedPeriods = false)
	{
		if ($requestedDate->timezone() != $this->targetTimezone)
		{
			throw new Exception('Target timezone and requested timezone do not match');
		}

		$cachedValues = $this->gtCachedValuesForDate($requestedDate);
		if (!empty($cachedValues))
		{
			return $cachedValues;
		}

		// check cache
		$baseDateInLayoutTz = RFDate::create($requestedDate->year(), $requestedDate->month(), $requestedDate->day(),
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
				$this->addDailyPeriods($adjustedDate->weekday(), $baseDateInLayoutTz, $requestedDate, $list);
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

	private function bothDatesAreOff(RFDate $start, RFDate $end, RFDate $layoutDate)
	{
		return !$start->dateEquals($layoutDate) && !$end->dateEquals($layoutDate);
	}

	private function buildPeriod($periodType, RFDate $start, RFDate $end, $label, $labelEnd = null)
	{
		return new $periodType($start, $end, $label, $labelEnd);
	}

	protected function sortItems(&$items)
	{
		usort($items, array("RFLayoutSchedule", "SortBeginTimes"));
	}

	public function timezone()
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
		$parser = new RFLayoutParser($timezone);
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
		if (count($reservableSlots) != RFDayOfWeek::NumberOfDays || count($blockedSlots) != RFDayOfWeek::NumberOfDays)
		{
			throw new Exception(sprintf('LayoutParser ParseDaily missing slots. $reservableSlots=%s, $blockedSlots=%s',
										count($reservableSlots), count($blockedSlots)));
		}
		$parser = new RFLayoutParser($timezone);

		foreach (RFDayOfWeek::Days() as $day)
		{
			$parser->addReservable($reservableSlots[$day], $day);
			$parser->addBlocked($blockedSlots[$day], $day);
		}

		return $parser->getLayout();
	}

	/**
	 * @param Date $date
	 * @return RFSchedulePeriod period which occurs at this datetime. Includes start time, excludes end time
	 */
	public function getPeriod(RFDate $date)
	{
		$timezone = $this->layoutTimezone;
		$tempDate = $date->toTimezone($timezone);
		$periods = $this->getPeriods($tempDate);

		/** @var $period LayoutPeriod */
		foreach ($periods as $period)
		{
			$start = RFDate::create($tempDate->year(), $tempDate->month(), $tempDate->day(), $period->start->hour(),
								  $period->start->Minute(), 0, $timezone);
			$end = RFDate::create($tempDate->year(), $tempDate->month(), $tempDate->day(), $period->end->hour(),
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

	private function getPeriods(RFDate $layoutDate)
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
