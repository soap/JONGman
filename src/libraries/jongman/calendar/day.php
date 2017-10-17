<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('jongman.calendar.icalendarsegment');

interface ICalendarDay
{
	public function Date();
	public function DayOfMonth();
	public function Weekday();
	public function IsHighlighted();
	public function IsUnimportant();

	public function AddReservation($reservation);
	public function GetAdjustedStartDate($reservation);
}

class RFCalendarDay implements ICalendarDay, ICalendarSegment
{
	/**
	 * @var \RFDate
	 */
	private $date;

	/**
	 * @var bool
	 */
	private $isHighlighted = false;

	/**
	 * @var array|CalendarReservation[]
	 */
	private $reservations = array();

	public function __construct(RFDate $date)
	{
		$this->date = $date->getDate();

		if ($this->date->dateEquals(RFDate::now()))
		{
			$this->highlight();
		}
	}

	/**
	 * @return int
	 */
	public function dayOfMonth()
	{
		return $this->date->day();
	}

	/**
	 * @return int
	 */
	public function weekday()
	{
		return $this->date->weekday();
	}

	/**
	 * @return int
	 */
	public function isHighlighted()
	{
		return $this->isHighlighted;
	}

	private function highlight()
	{
		$this->isHighlighted = true;
	}

	private static $nullInstance = null;

	/**
	 * @static
	 * @return CalendarDay
	 */
	public static function null()
	{
		if (self::$nullInstance == null)
		{
			self::$nullInstance = new RFNullCalendarDay();
		}
		return self::$nullInstance;
	}

	/**
	 * @return array|RFCalendarReservation[]
	 */
	public function getReservations()
	{
		return $this->reservations;
	}

	/**
	 * @param $reservation CalendarReservation
	 * @return void
	 */
	public function addReservation($reservation)
	{
		if ( ($this->startsBefore($reservation) || $this->startsOn($reservation)) && ($this->endsOn($reservation) || $this->endsAfter($reservation)) )
		{
			$this->reservations[] = $reservation;
		}
	}

	/**
	 * @param $reservation CalendarReservation
	 * @return bool
	 */
	private function startsBefore($reservation)
	{
		return $this->date->dateCompare($reservation->startDate) >= 0;
	}

	/**
	 * @param $reservation CalendarReservation
	 * @return bool
	 */
	private function startsOn($reservation)
	{
		return $this->date->dateEquals($reservation->startDate);
	}

	/**
	 * @param $reservation CalendarReservation
	 * @return bool
	 */
	private function endsAfter($reservation)
	{
		return $this->date->dateCompare($reservation->endDate) < 0;
	}

	/**
	 * @param $reservation CalendarReservation
	 * @return bool
	 */
	private function endsOn($reservation)
	{
		return $this->date->dateEquals($reservation->endDate);
	}

	/**
	 * @param $reservation CalendarReservation
	 * @return Date
	 */
	public function getAdjustedStartDate($reservation)
	{
		if ($reservation->startDate->dateCompare($this->date) < 0)
		{
			return $this->date;
		}

		return $reservation->startDate;
	}

	public function isUnimportant()
	{
		return false;
	}

	/**
	 * @return Date
	 */
	public function date()
	{
		return $this->date;
	}

	/**
	 * @return Date
	 */
	public function firstDay()
	{
		return $this->date->getDate();
	}

	/**
	 * @return Date
	 */
	public function lastDay()
	{
		return $this->date->addDays(1)->getDate();
	}

	/**
	 * @param $reservations array|CalendarReservation[]
	 * @return void
	 */
	public function addReservations($reservations)
	{
		/** @var $reservation CalendarReservation */
		foreach ($reservations as $reservation)
		{
			$this->addReservation($reservation);
		}
	}

	/**
	 * @return string|CalendarTypes
	 */
	public function getType()
	{
		return RFCalendarTypes::Day;
	}

	/**
	 * @return Date
	 */
	public function getPreviousDate()
	{
		return $this->date->addDays(-1);
	}

	/**
	 * @return Date
	 */
	public function getNextDate()
	{
		return $this->date->addDays(1);
	}
}

class RFNullCalendarDay implements ICalendarDay
{
	public function __construct()
	{
	}

	public function weekday()
	{
		return null;
	}

	public function isHighlighted()
	{
		return false;
	}

	public function dayOfMonth()
	{
		return null;
	}

	public function getReservations()
	{
		return array();
	}

	public function addReservation($reservation)
	{
		// no-op
	}

	public function getAdjustedStartDate($reservation)
	{
		return RFDateNull::getInstance();
	}

	public function isUnimportant()
	{
		return true;
	}

	public function date()
	{
		return RFDateNull::getInstance();
	}
}
