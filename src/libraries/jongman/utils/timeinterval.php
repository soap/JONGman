<?php
defined('_JEXEC') or die;

class RFTimeInterval
{
	/**
	 * @var RFDateDiff
	 */
	private $interval = null;
	
	/**
	 * @param int $seconds
	 */
	public function __construct($seconds)
	{
		$this->interval = null;
		
		if (!empty($seconds))
		{
			$this->interval = new RFDateDiff($seconds);
		}
	}

	/**
	 * @static
	 * @param string|int $interval string interval in format: #d#h#m ie: 22d4h12m or total seconds
	 * @return RFTimeInterval
	 */
	public static function parse($interval)
	{
		if (empty($interval))
		{
			return new RFTimeInterval(0);
		}

		if (!is_numeric($interval))
		{
			$seconds = RFDateDiff::fromTimeString($interval)->totalSeconds();
		}
		else
		{
			$seconds = $interval;
		}

		return new RFTimeInterval($seconds);
	}

	/**
	 * @return int
	 */
	public function days()
	{
		return $this->interval()->days();
	}

	/**
	 * @return int
	 */
	public function hours()
	{
		return $this->interval()->hours();
	}

	/**
	 * @return int
	 */
	public function minutes()
	{
		return $this->interval()->minutes();
	}
	
	/**
	 * @return RFDateDiff
	 */
	public function interval()
	{
		if ($this->interval != null)
		{
			return $this->interval;
		}

		return RFDateDiff::null();
	}

	/**
	 * @return null|int
	 */
	public function toDatabase()
	{
		if ($this->interval != null && !$this->interval->isNull())
		{
			return $this->interval->totalSeconds();
		}

		return null;
	}

	/**
	 * @return int
	 */
	public function totalSeconds()
	{
		return $this->interval->totalSeconds();
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		if ($this->interval != null)
		{
			return $this->interval->__toString();
		}
		
		return '';
	}
}