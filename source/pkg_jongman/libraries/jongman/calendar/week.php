<?php
defined('_JEXEC') or die;

jimport('jongman.calendar.icalendarsegment');

class RFCalendarWeek implements ICalendarSegment
{
	/**
	 * @var array|RFCalendarDay[]
	 */
	private $indexedDays = array();

	/**
	 * @var array|CalendarDay[]
	*/
	private $days = array();

	/**
	 * @var string
	*/
	private $timezone;

	/**
	 * @var array|RFCalendarReservation[]
	 */
	private $reservations;

	public function __construct($timezone)
	{
		$this->timezone = $timezone;

		for ($i = 0; $i < 7; $i++)
		{
			$this->indexedDays[$i] = RFCalendarDay::null();
		}
	}

	public static function fromDate($year, $month, $day, $timezone, $firstDayOfWeek = 0)
	{
		$week = new RFCalendarWeek($timezone);

		$date = RFDate::create($year, $month, $day, 0, 0, 0, $timezone);

		$start = $date->weekday();

		if ($firstDayOfWeek == RFSchedule::Today)
		{
			$firstDayOfWeek = 0;
		}

		$adjustedDays = ($firstDayOfWeek - $start);

		if ($start < $firstDayOfWeek)
		{
			$adjustedDays = $adjustedDays - 7;
		}

		$date = $date->addDays($adjustedDays);

		for ($i = 0; $i < 7; $i++)
		{
			$week->addDay(new RFCalendarDay($date->addDays($i)));
		}

		return $week;
	}

	public function firstDay()
	{
		return $this->days[0]->date();
	}

	public function lastDay()
	{
		return $this->days[count($this->days) - 1]->date();
	}

	public function addReservations($reservations)
	{
		/** @var $reservation CalendarReservation */
		foreach ($reservations as $reservation)
		{
			$this->addReservation($reservation);
		}
	}

	public function addDay(RFCalendarDay $day)
	{
		$this->days[] = $day;
		$this->indexedDays[$day->Weekday()] = $day;
	}

	/**
	 * @return array|ICalendarDay[]
	 */
	public function days()
	{
		return $this->indexedDays;
	}

	/**
	 * @param $reservation RFCalendarReservation
	 * @return void
	 */
	public function addReservation($reservation)
	{
		$this->reservations[] = $reservation;
		/** @var $day CalendarDay */
		foreach ($this->indexedDays as $day)
		{
			$day->addReservation($reservation);
		}
	}

	/**
	 * @return string|RFCalendarTypes
	 */
	public function getType()
	{
		return RFCalendarTypes::Week;
	}

	/**
	 * @return RFDate
	 */
	public function getPreviousDate()
	{
		return $this->firstDay()->addDays(-7);
	}

	/**
	 * @return RFDate
	 */
	public function getNextDate()
	{
		return $this->firstDay()->addDays(7);
	}

	/**
	 * @return array|RFCalendarReservation[]
	 */
	public function getReservations()
	{
		return $this->reservations;
	}
}