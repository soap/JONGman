<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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
jimport('jongman.date.date');
class Time
{
	private $_hour;
	private $_minute;
	private $_second;
	private $_timezone;
	
	const FORMAT_HOUR_MINUTE = "H:i";
	
	public function __construct($hour, $minute, $second = null, $timezone = null)
	{
		$this->_hour = intval($hour);
		$this->_minute =  intval($minute);
		$this->_second = is_null($second) ? 0 : intval($second);
		$this->_timezone = $timezone;
		
		if (empty($timezone))
    	{
    		$this->_timezone = JFactory::getConfig()->get('offset');
    	}
	}

    private function getDate()
    {
    	$parts = getdate(strtotime("$this->_hour:$this->_minute:$this->_second"));
    	return new JMDate("{$parts['year']}-{$parts['mon']}-{$parts['mday']} $this->_hour:$this->_minute:$this->_second", $this->_timezone);
    }
    
    /**
     * @param string $time
     * @param string $timezone, defaults to server timezone if not provided
     * @return Time
     */
    public static function parse($time, $timezone = null)
    {
    	$date = new JMDate($time, $timezone);
  
    	return new Time($date->hour(), $date->minute(), $date->second(), $timezone);
    }
	
	public function hour()
	{
		return $this->_hour;
	}
	
	public function minute()
	{
		return $this->_minute;
	}
	
	public function second()
	{
		return $this->_second;
	}
	
	public function timezone()
	{
		return $this->_timezone;
	}

	public function format($format)
	{
		return $this->getDate()->format($format);
	}
	
	public function toDatabase()
	{
		return $this->format('H:i:s');
	}
	
	/**
	 * Compares this time to the one passed in
	 * Returns:
	 * -1 if this time is less than the passed in time
	 * 0 if the times are equal
	 * 1 if this time is greater than the passed in time
	 * @param Time $time
	 * @param Date $comparisonDate date to be used for time comparison
	 * @return int comparison result
	 */
	public function compare(Time $time, $comparisonDate = null)
	{
		if ($comparisonDate != null)
		{
			$myDate = JMDate::create($comparisonDate->year(), $comparisonDate->month(), $comparisonDate->day(), $this->hour(), $this->minute(), $this->second(), $this->timezone());
			$otherDate = JMDate::create($comparisonDate->year(), $comparisonDate->month(), $comparisonDate->day(), $time->hour(), $time->minute(), $time->second(), $time->timezone());
			
			return ($myDate->compare($otherDate));
		}
		
		return $this->getDate()->compare($time->getDate());
	}
	
	public function toString()
	{
		return sprintf("%02d:%02d:%02d", $this->_hour, $this->_minute, $this->_second);
	}
	
	public function __toString() 
	{
      return $this->toString();
  	}
}

class NullTime extends Time
{
	public function __construct()
	{
		parent::__construct(0, 0, 0, null);
	}
	
	public function toDatabase()
	{
		return null;
	}
	
	public function toString()
	{
		return '';
	}
}
?>