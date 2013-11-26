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

class PeriodTypes
{
	const RESERVABLE = 1;
	const NONRESERVABLE = 2;
}

class SchedulePeriod
{
	/**
	 * @var Date
	 */
	protected $_begin;

	/**
	 * @var Date
	 */
	protected $_end;

	protected $_label;

	protected $_id;

	public function __construct(JMDate $begin, JMDate $end, $label = null)
	{
		$this->_begin = $begin;
		$this->_end = $end;
		$this->_label = $label;
	}

	/**
	 * @return Time beginning time for this period
	 */
	public function begin()
	{
		return $this->_begin->getTime();
	}

	/**
	 * @return Time ending time for this period
	 */
	public function end()
	{
		return $this->_end->getTime();
	}

	/**
	 * @return Date
	 */
	public function beginDate()
	{
		return $this->_begin;
	}

	/**
	 * @return Date
	 */
	public function endDate()
	{
		return $this->_end;
	}

	/**
	 * @param Date $dateOverride
	 * @return string
	 */
	public function label($dateOverride = null)
	{
		if (empty($this->_label))
		{
			$format = JComponentHelper::getParams('com_jongman')->get('time_format', 'H:i');

			if (isset($dateOverride) && !$this->_begin->dateEquals($dateOverride))
			{
				return $dateOverride->format($format);
			}
			return $this->_begin->format($format);
		}
		return $this->_label;
	}

	/**
	 * @return string
	 */
	public function labelEnd()
	{
		if (empty($this->_label))
		{
			$format = JComponentHelper::getParams('com_jongman')->get('time_format', 'H:i');

			return $this->_end->format($format);
		}
		return '(' . $this->_label . ')';
	}

	/**
	 * @return bool
	 */
	public function isReservable()
	{
		return true;
	}

	public function isLabelled()
	{
		return !empty($this->_label);
	}

	public function toUtc()
	{
		return new SchedulePeriod($this->_begin->toUtc(), $this->_end->toUtc(), $this->_label);
	}

	public function toTimezone($timezone)
	{
		return new SchedulePeriod($this->_begin->toTimezone($timezone), $this->_end->toTimezone($timezone), $this->_label);
	}

	public function __toString()
	{
		return sprintf("Begin: %s End: %s Label: %s", $this->_begin, $this->_end, $this->Label());
	}

	/**
	 * Compares the starting datetimes
	 */
	public function compare(SchedulePeriod $other)
	{
		return $this->_begin->compare($other->_begin);
	}

	public function beginsBefore(Date $date)
	{
		return $this->_begin->dateCompare($date) < 0;
	}

	/**
	 * @return string
	 */
	public function Id()
	{
		if (empty($this->_id))
		{
			$this->_id = uniqid();
		}
		return $this->_id;
	}
}

class NonSchedulePeriod extends SchedulePeriod
{
	public function isReservable()
	{
		return false;
	}

	public function toUtc()
	{
		return new NonSchedulePeriod($this->_begin->toUtc(), $this->_end->toUtc(), $this->_label);
	}

	public function toTimezone($timezone)
	{
		return new NonSchedulePeriod($this->_begin->toTimezone($timezone), $this->_end->toTimezone($timezone), $this->_label);
	}
}
