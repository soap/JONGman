<?php
defined('_JEXEC') or die;

jimport('jongman.application.domain.*');
jimport('jongman.application.schedule.schedulereservationlist');

interface IDailyLayout
{
	/**
	 * @param Date $date
	 * @param int $resourceId
	 * @return array|IReservationSlot[]
	 */
	function getLayout(JMDate $date, $resourceId);

	/**
	 * @param Date $date
	 * @return bool
	 */
	function isDateReservable(JMDate $date);

	/**
	 * @param Date $displayDate
	 * @return string[]
	 */
	function getLabels(JMDate $displayDate);

	/**
	 * @param Date $displayDate
	 * @return mixed
	 */
	function getPeriods(JMDate $displayDate);
}

class DailyLayout implements IDailyLayout
{
	/**
	 * @var IReservationListing
	 */
	private $_reservationListing;
	/**
	 * @var IScheduleLayout
	 */
	private $_scheduleLayout;

	/**
	 * @param IReservationListing $listing List of reservation data for schedule
	 * @param IScheduleLayout $layout schedule layout blocks
	 */
	public function __construct(IReservationListing $listing, IScheduleLayout $layout)
	{
		// Just store the provided data
		$this->_reservationListing = $listing;
		$this->_scheduleLayout = $layout;
	}

	/**
	 * Get display slots for resource specified by $resourceId on date specified by $date
	 * @see IDailyLayout::getLayout()
	 */
	public function getLayout(JMDate $date, $resourceId)
	{
		$hideBlocked = false;
		$items = $this->_reservationListing->onDateForResource($date, $resourceId);

		$list = new ScheduleReservationList($items, $this->_scheduleLayout, $date, $hideBlocked);
		$slots = $list->buildSlots();

		return $slots;
	}
	
	/**
	 * check if the provided date is reservable
	 * @see IDailyLayout::isDateReservable()
	 */
	public function isDateReservable(JMDate $date)
	{
		return !$date->getDate()->lessThan(JMDate::now()->getDate());
	}

	public function getLabels(JMDate $displayDate)
	{
		$hideBlocked = false;

		$labels = array();

		$periods = $this->_scheduleLayout->getLayout($displayDate, $hideBlocked);

		if ($periods[0]->beginsBefore($displayDate))
		{
			$labels[] = $periods[0]->label($displayDate->getDate());
		}
		else
		{
			$labels[] = $periods[0]->label();
		}

		for ($i = 1; $i < count($periods); $i++)
		{
			$labels[] = $periods[$i]->label();
		}

		return $labels;
	}

	/**
	 * Get periods on the date for the current schedule
	 * @see IDailyLayout::getPeriods()
	 */
	public function getPeriods(JMDate $displayDate, $fitToHours = false)
	{
		$hideBlocked = false;

		$periods = $this->_scheduleLayout->GetLayout($displayDate, $hideBlocked);

		if (!$fitToHours)
		{
			return $periods;
		}

		/** @var $periodsToReturn SpanablePeriod[] */
		$periodsToReturn = array();

		for ($i = 0; $i < count($periods); $i++)
		{
			$span = 1;
			$currentPeriod = $periods[$i];
			$periodStart = $currentPeriod->beginDate();
			$periodLength = $periodStart->getDifference($currentPeriod->endDate())->hours();

			if (!$periods[$i]->isLabelled() && ($periodStart->minute() == 0 && $periodLength < 1))
			{
				$span = 0;
				$nextPeriodTime = $periodStart->addMinutes(60);
				$tempPeriod = $currentPeriod;
				while ($tempPeriod != null && $tempPeriod->beginDate()->lessThan($nextPeriodTime))
				{
					$span++;
					$i++;
					$tempPeriod = $periods[$i];
				}
				$i--;

			}
			$periodsToReturn[] = new SpanablePeriod($currentPeriod, $span);

		}

		return $periodsToReturn;
	}
}

interface IDailyLayoutFactory
{
	/**
	 * @param IReservationListing $listing
	 * @param IScheduleLayout $layout
	 * @return IDailyLayout
	 */
	function Create(IReservationListing $listing, IScheduleLayout $layout);
}

class DailyLayoutFactory implements IDailyLayoutFactory
{
	public function create(IReservationListing $listing, IScheduleLayout $layout)
	{
		return new DailyLayout($listing, $layout);
	}
}

class SpanablePeriod extends SchedulePeriod
{
	private $span = 1;
	private $period;

	public function __construct(SchedulePeriod $period, $span = 1)
	{
		$this->span = $span;
		$this->period = $period;
		parent::__construct($period->beginDate(), $period->endDate(), $period->_label);

	}

	public function span()
	{
		return $this->span;
	}

	public function setSpan($span)
	{
		$this->span = $span;
	}

	public function isReservable()
	{
		return $this->period->isReservable();
	}
}
