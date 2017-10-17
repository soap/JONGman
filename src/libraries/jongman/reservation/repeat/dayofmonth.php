<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRepeatDayofmonth extends RFReservationRepeatAbstract
{
	/**
	 * @param int $interval
	 * @param Date $terminationDate
	 */
	public function __construct($interval, $terminationDate)
	{
		parent::__construct($interval, $terminationDate);
	}

	public function getDates(RFDateRange $startingRange)
	{
		$dates = array();

		$startDate = $startingRange->getBegin();
		$endDate = $startingRange->getEnd();

		$rawStart = $startingRange->getBegin();
		$rawEnd = $startingRange->getEnd();

		$monthsFromStart = 1;
		while ($startDate->dateCompare($this->_terminationDate) <= 0)
		{
			$monthAdjustment = $monthsFromStart * $this->_interval;
			if ($this->dayExistsInNextMonth($rawStart, $monthAdjustment))
			{
				$startDate = $this->getNextMonth($rawStart, $monthAdjustment);
				$endDate = $this->getNextMonth($rawEnd, $monthAdjustment);
				if ($startDate->dateCompare($this->_terminationDate) <= 0)
				{
					$dates[] = new RFDateRange($startDate->toUtc(), $endDate->toUtc());
				}
			}
			$monthsFromStart++;
		}

		return $dates;
	}

	public function repeatType()
	{
		return RFReservationRepeatType::MONTHLY;
	}

	public function configurationString()
	{
		$obj = new JRegistry();
		$obj->loadString(parent::configurationString());		
		$obj->set('repeat_monthly_type', RFReservationRepeatMonthlytype::DayOfMonth);
		return $obj->toString();
	}

	private function dayExistsInNextMonth($date, $monthsFromStart)
	{
		$dateToCheck = RFDate::create($date->Year(), $date->Month(), 1, 0, 0, 0, $date->timezone());
		$nextMonth = $this->getNextMonth($dateToCheck, $monthsFromStart);

		$daysInMonth = $nextMonth->format('t');
		return $date->day() <= $daysInMonth;
	}

	/**
	 * @var Date $date
	 * @return Date
	 */
	private function getNextMonth($date, $monthsFromStart)
	{
		$yearOffset = 0;
		$computedMonth = $date->month() + $monthsFromStart;
		$month = $computedMonth;

		if ($computedMonth > 12)
		{
			$yearOffset = (int)($computedMonth - 1) / 12;
			$month = ($computedMonth - 1) % 12 + 1;
		}

		return RFDate::create($date->year() + $yearOffset, $month, $date->day(), $date->hour(), $date->minute(),
							$date->second(), $date->timezone());
	}
}