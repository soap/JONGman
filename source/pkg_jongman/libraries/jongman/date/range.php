<?php
/**
Copyright 2011-2013 Nick Korbel

This file is part of phpScheduleIt.

phpScheduleIt is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

phpScheduleIt is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with phpScheduleIt.  If not, see <http://www.gnu.org/licenses/>.
*/

class RFDateRange
{
	/**
	 * @var Date
	 */
	private $_begin;
	
	/**
	 * @var Date
	 */
	private $_end;

	/**
	 * @var string
	 */
	private $_timezone;

	/**
	 * @param Date $begin
	 * @param Date $end
	 * @param string $timezone
	 */
	public function __construct(RFDate $begin, RFDate $end, $timezone = null)
	{
		$this->_begin = $begin;
		$this->_end = $end;

		if (empty($timezone))
		{
			$this->_timezone = $begin->timezone();
		}
		else
		{
			$this->_timezone = $timezone;
		}
	}

	/**
	 * @param string $beginString
	 * @param string $endString
	 * @param string $timezoneString
	 * @return DateRange
	 */
	public static function create($beginString, $endString, $timezoneString)
	{
		return new DateRange(RFDate::parse($beginString, $timezoneString), RFDate::parse($endString, $timezoneString), $timezoneString);
	}

	/**
	 * Whether or not the $date is within the range.  Range boundaries are inclusive
	 * @param Date $date
	 * @return bool
	 */
	public function contains(RFDate $date)
	{
		return $this->_begin->compare($date) <= 0 && $this->_end->compare($date) >= 0;
	}

	/**
	 * @param DateRange $dateRange
	 * @return bool
	 */
	public function containsRange(DateRange $dateRange)
	{
		return $this->_begin->compare($dateRange->_begin) <= 0 && $this->_end->compare($dateRange->_end) >= 0;
	}

	/**
	 * Whether or not the date ranges overlap.  Dates that start or end on boundaries are excluded
	 * @param DateRange $dateRange
	 * @return bool
	 */
	public function overlaps(DateRange $dateRange)
	{
		return (	$this->contains($dateRange->getBegin()) 
					|| $this->contains($dateRange->getEnd()) 
					|| $dateRange->contains($this->getBegin()) 
					|| $dateRange->contains($this->getEnd())) 
					&&
					(!$this->getBegin()->equals($dateRange->getEnd()) 
					&& !$this->getEnd()->equals($dateRange->getBegin())
				);

	}

	/**
	 * Whether or not any date within this range occurs on the provided date
	 * @param Date $date
	 * @return bool
	 */
	public function occursOn(RFDate $date)
	{
		$timezone = $date->timezone();
		$compare = $this;

		if ($timezone != $this->_timezone)
		{
			$compare = $this->toTimezone($timezone);
		}

		$beginMidnight = $compare->getBegin();

		if ($this->getEnd()->isMidnight())
		{
			$endMidnight = $compare->getEnd();
		}
		else
		{
			$endMidnight = $compare->getEnd()->addDays(1);
		}

		return ($beginMidnight->dateCompare($date) <= 0 &&
				$endMidnight->dateCompare($date) > 0);
	}

	/**
	 * @return Date
	 */
	public function getBegin()
	{
		return $this->_begin;	
	}

	/**
	 * @return Date
	 */
	public function getEnd()
	{
		return $this->_end;
	}
	
	/**
	 * @return array[int] RFDate
	 */
	public function dates()
	{
		$current = $this->_begin->getDate();
		$end = $this->_end->getDate();
		
		$dates = array($current);
		
		for($day = 0; $current->compare($end) < 0; $day++)
		{
			$current = $current->addDays(1);
			$dates[] = $current;
		}
		
		return $dates;
	}
	
	/**
	 * @param DateRange $otherRange
	 * @return bool
	 */
	public function equals(DateRange $otherRange)
	{
		return $this->_begin->equals($otherRange->getBegin()) && $this->_end->equals($otherRange->getEnd());
	}
	
	/**
	 * @param string $timezone
	 * @return DateRange
	 */
	public function toTimezone($timezone)
	{
		return new DateRange($this->_begin->toTimezone($timezone), $this->_end->toTimezone($timezone));
	}
	
	/**
	 * @return DateRange
	 */
	public function toUtc()
	{
		return new DateRange($this->_begin->toUtc(), $this->_end->toUtc());
	}
	
	/**
	 * @param int $days
	 * @return DateRange
	 */
	public function addDays($days)
	{
		return new DateRange($this->_begin->addDays($days), $this->_end->addDays($days));
	}
	
	/**
	 * @return string
	 */
	public function toString()
	{
		return "\nBegin: " . $this->_begin->toString() . " End: " . $this->_end->toString() . "\n";
	}
	
	public function __toString()
	{
		return $this->ToString();
	}
}

class NullDateRange extends RFDateRange
{
	protected static $instance;
	
	public function __construct()
	{
		parent::__construct(RFDate::Now(), RFDate::Now());
	}
	
	/**
	 * @return NullDateRange
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			self::$instance = new NullDateRange();
		}
		
		return self::$instance;
	}
}
