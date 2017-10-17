<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('jongman.calendar.icalendarsegment');

class RFCalendarMonth implements ICalendarSegment
{
	private $month;
	private $year;
	private $timezone;
	private $firstDay;
	private $lastDay;

	/**
	 * @var array|CalendarWeek[]
	 */
	private $weeks = array();

	/**
	 * @var array|RFCalendarReservation[]
	*/
	private $reservations = array();

	public function __construct($month, $year, $timezone)
	{
		$this->month = $month;
		$this->year = $year;
		$this->timezone = $timezone;

		$this->firstDay = RFDate::create($this->year, $this->month, 1, 0, 0, 0, $this->timezone);
		$this->lastDay = $this->firstDay->addMonths(1);

		$daysInMonth = $this->lastDay->addDays(-1)->day();

		$weeks = floor(($daysInMonth + $this->firstDay->weekday()-1) / 7);

		for ($week = 0; $week <= $weeks; $week++)
		{
			$this->weeks[$week] = new RFCalendarWeek($timezone);
		}

		for ($dayOffset = 0; $dayOffset < $daysInMonth; $dayOffset++)
		{
			$currentDay = $this->firstDay->addDays($dayOffset);
			$currentWeek = $this->getWeekNumber($currentDay);
			$calendarDay = new RFCalendarDay($currentDay);

			$this->weeks[$currentWeek]->addDay($calendarDay);
		}
	}

	public function weeks()
	{
		return $this->weeks;
	}

	public function firstDay()
	{
		return $this->firstDay;
	}

	public function lastDay()
	{
		return $this->lastDay;
	}

	/**
	 * @param $reservations array|RFCalendarReservation[]
	 * @return void
	 */
	public function addReservations($reservations)
	{
		/** @var $reservation CalendarReservation */
		foreach ($reservations as $reservation)
		{
			$this->reservations[] = $reservation;

			/** @var $week CalendarWeek */
			foreach ($this->weeks() as $week)
			{
				$week->addReservation($reservation);
			}
		}
	}

	/**
	 * @param Date $day
	 * @return int
	 */
	private function getWeekNumber(RFDate $day)
	{
		$firstWeekday = $this->firstDay->weekday();

		$week = floor($day->day()/7);

		if ($day->day()%7==0)
		{
			$week = ($day->day()-1)/7;

			if ($day->day() <= 7)
			{
				$week++;
			}
		}
		else
		{
			if ($day->weekday() < $firstWeekday)
			{
				$week++;
			}
		}

		return intval($week);
	}


	/**
	 * @return string|CalendarTypes
	 */
	public function getType()
	{
		return RFCalendarTypes::Month;
	}

	/**
	 * @return Date
	 */
	public function getPreviousDate()
	{
		return $this->firstDay()->addMonths(-1);
	}

	/**
	 * @return Date
	 */
	public function getNextDate()
	{
		return $this->firstDay()->addMonths(1);
	}

	/**
	 * @return array|RFCalendarReservation[]
	 */
	public function getReservations()
	{
		return $this->reservations;
	}
}