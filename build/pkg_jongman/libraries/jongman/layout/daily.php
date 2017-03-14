<?php
defined('_JEXEC') or die;


interface ILayoutDaily
{
	/**
	 * @param Date $date
	 * @param int $resourceId
	 * @return array|IReservationSlot[]
	 */
	function getLayout(RFDate $date, $resourceId);

	/**
	 * @param Date $date
	 * @return bool
	 */
	function isDateReservable(RFDate $date);

	/**
	 * @param Date $displayDate
	 * @return string[]
	 */
	function getLabels(RFDate $displayDate);

	/**
	 * @param Date $displayDate
	 * @return mixed
	 */
	function getPeriods(RFDate $displayDate);
}

class RFLayoutDaily implements ILayoutDaily
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
	public function __construct(IReservationListing $listing, ILayoutSchedule $layout)
	{
		// Just store the provided data
		$this->_reservationListing = $listing;
		$this->_scheduleLayout = $layout;
	}

	/**
	 * Get display slots for resource specified by $resourceId on date specified by $date
	 * @see IDailyLayout::getLayout()
	 */
	public function getLayout(RFDate $date, $resourceId)
	{
		$hideBlocked = false;
		$items = $this->_reservationListing->onDateForResource($date, $resourceId);

		$list = new RFScheduleReservationList($items, $this->_scheduleLayout, $date, $hideBlocked);
		$slots = $list->buildSlots();

		return $slots;
	}
	
	/**
	 * check if the provided date is reservable
	 * @see IDailyLayout::isDateReservable()
	 */
	public function isDateReservable(RFDate $date)
	{
		return !$date->getDate()->lessThan(RFDate::now()->getDate());
	}

	public function getLabels(RFDate $displayDate)
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
	public function getPeriods(RFDate $displayDate, $fitToHours = false)
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
			$periodsToReturn[] = new RFSchedulePeriodSpanable($currentPeriod, $span);

		}

		return $periodsToReturn;
	}
}
