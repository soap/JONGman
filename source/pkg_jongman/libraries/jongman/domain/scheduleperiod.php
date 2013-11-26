<?php
defined('_JEXEC') or die;

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
