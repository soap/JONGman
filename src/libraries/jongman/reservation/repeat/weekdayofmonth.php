<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRepeatWeekdayofmonth extends RFReservationRepeatAbstract
{
	private $_typeList = array(1 => 'first', 2 => 'second', 3 => 'third', 4 => 'fourth', 5 => 'fifth');
	private $_dayList = array(0 => 'sunday', 1 => 'monday', 2 => 'tuesday', 3 => 'wednesday', 4 => 'thursday', 5 => 'friday', 6 => 'saturday');

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

		$durationStart = $startingRange->getBegin();
		$firstWeekdayOfMonth = date('w', mktime(0, 0, 0, $durationStart->month(), 1, $durationStart->year()));

		$weekNumber = $this->getWeekNumber($durationStart, $firstWeekdayOfMonth);
		$dayOfWeek = $durationStart->weekday();
		$startMonth = $durationStart->month();
		$startYear = $durationStart->year();

		$monthsFromStart = 1;
		while ($startDate->dateCompare($this->_terminationDate) <= 0)
		{
			$computedMonth = $startMonth + $monthsFromStart * $this->_interval;
			$month = ($computedMonth - 1) % 12 + 1;
			$year = $startYear + (int)(($computedMonth - 1) / 12);

			$correctedWeekNumber = $this->getWeekNumberOfMonth($weekNumber, $month, $year, $dayOfWeek);

			$dayOfMonth = strtotime("{$this->_typeList[$correctedWeekNumber ]} {$this->_dayList[$dayOfWeek]} $year-$month-01");
			$calculatedDate = date('Y-m-d', $dayOfMonth);
			$calculatedMonth = explode('-', $calculatedDate);

			$startDateString = $calculatedDate . " {$startDate->hour()}:{$startDate->minute()}:{$startDate->second()}";
			$startDate = RFDate::Parse($startDateString, $startDate->timezone());

			if ($month == $calculatedMonth[1])
			{
				if ($startDate->dateCompare($this->_terminationDate) <= 0)
				{
					$endDateString = $calculatedDate . " {$endDate->hour()}:{$endDate->minute()}:{$endDate->second()}";
					$endDate = RFDate::parse($endDateString, $endDate->timezone());

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
		$obj->set('repeat_monthly_type', RFReservationRepeatMonthlytype::DayOfWeek);
		return $obj->toString();
	}

	private function getWeekNumber(RFDate $firstDate, $firstWeekdayOfMonth)
	{
		$week = ceil($firstDate->Day() / 7);
//		if ($firstWeekdayOfMonth > $firstDate->Weekday())
//		{
//			$week++;
//		}

		return $week;
	}

	private function getWeekNumberOfMonth($week, $month, $year, $desiredDayOfWeek)
	{
		$firstWeekdayOfMonth = date('w', mktime(0, 0, 0, $month, 1, $year));

		$weekNumber = $week;
		if ($firstWeekdayOfMonth == $desiredDayOfWeek)
		{
			$weekNumber--;
		}

		return $weekNumber;
	}
}
