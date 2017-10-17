<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFDateDiff
{
	/**
	 * @var int
	 */
	private $seconds = 0;

	/**
	 * @param int $seconds
	 */
	public function __construct($seconds)
	{
		$this->seconds = intval($seconds);
	}

	/**
	 * @return int
	 */
	public function totalSeconds()
	{
		return $this->seconds;
	}

	public function days()
	{
		$days = intval($this->seconds / 86400);
		return $days;
	}

	public function hours()
	{
		$hours = intval($this->seconds / 3600) - intval($this->Days() * 24);
		return $hours;
	}

	public function minutes()
	{
		$minutes = intval(($this->seconds / 60) % 60);
		return $minutes;
	}

	/**
	 * @static
	 * @param Date $date1
	 * @param Date $date2
	 * @return DateDiff
	 */
	public static function betweenDates(RFDate $date1, RFDate $date2)
	{
		if ($date1->equals($date2))
		{
			return RFDateDiff::null();
		}

		$compareDate = $date2;
		if ($date1->timezone() != $date2->timezone())
		{
			$compareDate = $date2->toTimezone($date1->timezone());
		}

		return new RFDateDiff($compareDate->timestamp() - $date1->timestamp());
	}

	/**
	 * @static
	 * @param string $timeString in #d#h#m, for example 2d22h13m for 2 days 22 hours 13 minutes
	 * @return DateDiff
	 */
	public static function fromTimeString($timeString)
	{
		if (strpos($timeString, 'd') === false && strpos($timeString, 'h') === false && strpos($timeString,
																							   'm') === false
		)
		{
			throw new Exception('Time format must contain at least a day, hour or minute. For example: 12d1h22m');
		}

		$matches = array();

		preg_match('/(\d*d)?(\d*h)?(\d*m)?/i', $timeString, $matches);

		$day = 0;
		$hour = 0;
		$minute = 0;
		$num_set = 0;

		if (isset($matches[1]))
		{
			$num_set++;
			$day = intval(substr($matches[1], 0, -1));
		}
		if (isset($matches[2]))
		{
			$num_set++;
			$hour = intval(substr($matches[2], 0, -1));
		}
		if (isset($matches[3]))
		{
			$num_set++;
			$minute = intval(substr($matches[3], 0, -1));
		}

		if ($num_set == 0)
		{
			/**
			 * We didn't actually match anything, throw an exception
			 * instead of silently returning 0
			 */

			throw new Exception('Time format must be in day, hour, minute order');
		}

		return self::create($day, $hour, $minute);
	}

	/**
	 * @static
	 * @param int $days
	 * @param int $hours
	 * @param int $minutes
	 * @return DateDiff
	 */
	public static function create($days, $hours, $minutes)
	{
		return new DateDiff(($days * 24 * 60 * 60) + ($hours * 60 * 60) + ($minutes * 60));
	}

	/**
	 * @static
	 * @return DateDiff
	 */
	public static function null()
	{
		return new RFDateDiff(0);
	}

	/**
	 * @return bool
	 */
	public function isNull()
	{
		return $this->seconds == 0;
	}

	/**
	 * @param DateDiff $diff
	 * @return DateDiff
	 */
	public function add(RFDatediff $diff)
	{
		return new RFDateDiff($this->seconds + $diff->seconds);
	}

	/**
	 * @param DateDiff $diff
	 * @return bool
	 */
	public function greaterThan(RFDateDiff $diff)
	{
		return $this->seconds > $diff->seconds;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		$str = '';

		if ($this->days() > 0)
		{
			$str .= $this->days() . ' days ';
		}
		if ($this->hours() > 0)
		{
			$str .= $this->hours() . ' hours ';
		}
		if ($this->minutes() > 0)
		{
			$str .= $this->minutes() . ' minutes';
		}

		return $str;
	}
}