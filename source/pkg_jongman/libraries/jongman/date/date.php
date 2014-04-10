<?php
defined('_JEXEC') or die;

class RFDate
{
	/**
	 * @var DateTime
	 */
	private $date;
	private $parts;
	private $timezone;
	private $timestamp;
	private $timestring;

	const SHORT_FORMAT = "Y-m-d H:i:s";

	// Only used for testing
	private static $_Now = null;

	/**
	 * Creates a Date with the provided timestamp and timezone
	 * Defaults to current time
	 * Defaults to server.timezone configuration setting
	 *
	 * @param string $timestring
	 * @param string $timezone
	 */
	public function __construct($timestring = null, $timezone = null)
	{
		$this->initTimezone($timezone);
		if (empty($this->timezone)) {
			
		}
		$this->date = new DateTime($timestring, new DateTimeZone($this->timezone));
		$this->timestring = $this->date->format(self::SHORT_FORMAT);
		$this->timestamp = $this->date->format('U');
		$this->initParts();
	}

	private function initTimezone($timezone)
	{
		$this->timezone = $timezone;
		if (empty($timezone))
		{
			$tz = JongmanHelper::getUserTimezone();
			$this->timezone = $tz ;
		}
	}

	/**
	 * Create a new RFDate object with the given year, month, day, and optional hour, minute, second and timezone
     * @param int $year
     * @param int $month
     * @param int $day
     * @param int $hour
     * @param int $minute
     * @param int $second
     * @param string $timezone
	 * @return RFDate
	 */
	public static function create($year, $month, $day, $hour = 0, $minute = 0, $second = 0, $timezone = null)
	{
		if ($month > 12)
		{
			$yearOffset = floor($month / 12);
			$year = $year + $yearOffset;
			$month = $month - ($yearOffset * 12);
		}

		return new RFDate(sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute,
								$second), $timezone);
	}

	/**
	 * Create a new RFDate object from the given string and $timezone
	 * @param string $dateString
	 * @param string|null $timezone
	 * @return RFDate
	 */
	public static function parse($dateString, $timezone = null)
	{
		if (empty($dateString))
		{
			return RFDateNull::getInstance();
		}
		return new RFDate($dateString, $timezone);
	}

	/**
	 * @param string $dateString
	 * @return RFDate
	 */
	public static function parseExact($dateString)
	{
		if (empty($dateString))
		{
			return RFDateNull::getInstance();
		}

		$date = new DateTime($dateString);
		$timeOffsetString = $date->getTimezone()->getName();
		$offsetParts = explode(':', $timeOffsetString);

		$d = new RFDate($date->format(RFDate::SHORT_FORMAT), 'UTC');
		$offsetMinutes = ($offsetParts[0] * -60) + $offsetParts[1];
		return $d->addMinutes($offsetMinutes);
	}

	/**
	 * Returns a Date object representing the current date/time in the server's timezone
	 *
	 * @return RFDate
	 */
	public static function now()
	{
		if (isset(self::$_Now))
		{
			return self::$_Now;
		}

		return new RFDate('now');
	}

	/**
	 * Formats the Date with the provided format
	 *
	 * @param string $format
	 * @return string
	 */
	public function format($format)
	{
		return $this->date->format($format);
	}

	/**
	 * Returns the Date adjusted into the provided timezone
	 *
	 * @param string $timezone
	 * @return RFDate
	 */
	public function toTimezone($timezone)
	{
		if ($this->timezone() == $timezone)
		{
			return $this->copy();
		}

		$date = new DateTime($this->timestring, new DateTimeZone($this->timezone));

		$date->setTimezone(new DateTimeZone($timezone));
		$adjustedDate = $date->format(RFDate::SHORT_FORMAT);

		return new RFDate($adjustedDate, $timezone);
	}

	/**
	 * @return RFDate
	 */
	public function copy()
	{
		return new RFDate($this->timestring, $this->timezone());
	}

	/**
	 * Returns the Date adjusted into UTC
	 *
	 * @return RFDate
	 */
	public function toUtc()
	{
		return $this->toTimezone('UTC');
	}

	/**
	 * @return string
	 */
	public function toIso()
	{
		return $this->format(DateTime::ISO8601);
	}

	/**
	 * Formats the Date into a format that is accepted by the database
	 * @return string
	 */
	public function toDatabase()
	{
		return $this->toUtc()->format('Y-m-d H:i:s');
	}

	/**
	 * @param string $databaseValue
	 * @return RFDate
	 */
	public static function fromDatabase($databaseValue)
	{
		if (empty($databaseValue))
		{
			return RFDateNull::getInstance();
		}
		return RFDate::parse($databaseValue, 'UTC');
	}

	/**
	 * Returns the current Date as a timestamp
	 *
	 * @return int
	 */
	public function timestamp()
	{
		return intval($this->timestamp);
	}

	/**
	 * Returns the Time part of the Date
	 *
	 * @return RFTime
	 */
	public function getTime()
	{
		return new RFTime($this->hour(), $this->minute(), $this->second(), $this->timezone());
	}

	/**
	 * Returns the Date only part of the date.  Hours, Minutes and Seconds will be 0
	 *
	 * @return RFDate
	 */
	public function getDate()
	{
		return RFDate::create($this->year(), $this->month(), $this->day(), 0, 0, 0, $this->timezone());
	}

	/**
	 * Compares this date to the one passed in
	 * Returns:
	 * -1 if this date is less than the passed in date
	 * 0 if the dates are equal
	 * 1 if this date is greater than the passed in date
	 * @param RFDate $date
	 * @return int comparison result
	 */
	public function compare(RFDate $date)
	{
		$date2 = $date;
		if ($date2->timezone() != $this->timezone())
		{
			$date2 = $date->toTimezone($this->timezone);
		}

		if ($this->timestamp() < $date2->timestamp())
		{
			return -1;
		}
		else
		{
			if ($this->timestamp() > $date2->timestamp())
			{
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Compares the time component of this date to the one passed in
	 * Returns:
	 * -1 if this time is less than the passed in time
	 * 0 if the times are equal
	 * 1 if this times is greater than the passed in times
	 * @param RFDate $date
	 * @return int comparison result
	 */
	public function compareTime(RFDate $date)
	{
		$date2 = $date;
		if ($date2->timezone() != $this->timezone())
		{
			$date2 = $date->toTimezone($this->timezone);
		}

		$hourCompare = ($this->hour() - $date2->hour());
		$minuteCompare = ($this->minute() - $date2->minute());
		$secondCompare = ($this->second() - $date2->second());

		if ($hourCompare < 0 || ($hourCompare == 0 && $minuteCompare < 0) || ($hourCompare == 0 && $minuteCompare == 0 && $secondCompare < 0))
		{
			return -1;
		}
		else
		{
			if ($hourCompare > 0 || ($hourCompare == 0 && $minuteCompare > 0) || ($hourCompare == 0 && $minuteCompare == 0 && $secondCompare > 0))
			{
				return 1;
			}
		}

		return 0;
	}

	/**
	 * Compares this date to the one passed in
	 * @param RFDate $end
	 * @return bool if the current object is greater than the one passed in
	 */
	public function greaterThan(RFDate $end)
	{
		return $this->compare($end) > 0;
	}

	/**
	 * Compares this date to the one passed in
	 * @param RFDate $end
	 * @return bool if the current object is less than the one passed in
	 */
	public function lessThan(RFDate $end)
	{
		return $this->compare($end) < 0;
	}

	/**
	 * Compare the 2 dates
	 *
	 * @param RFDate $date
	 * @return bool
	 */
	public function equals(RFDate $date)
	{
		return $this->compare($date) == 0;
	}

	/**
	 * @param RFDate $date
	 * @return bool
	 */
	public function dateEquals(RFDate $date)
	{
		$date2 = $date;
		if ($date2->timezone() != $this->timezone())
		{
			$date2 = $date->toTimezone($this->timezone);
		}

		return ($this->day() == $date2->day() && $this->month() == $date2->month() && $this->year() == $date2->year());
	}

	public function dateCompare(RFDate $date)
	{
		$date2 = $date;
		if ($date2->timezone() != $this->timezone())
		{
			$date2 = $date->toTimezone($this->timezone);
		}

		$d1 = (int)$this->format('Ymd');
		$d2 = (int)$date2->format('Ymd');

		if ($d1 > $d2)
		{
			return 1;
		}
		if ($d1 < $d2)
		{
			return -1;
		}
		return 0;
	}

	/**
	 * @return bool
	 */
	public function isMidnight()
	{
		return $this->hour() == 0 && $this->minute() == 0 && $this->second() == 0;
	}

	/**
	 * @param int $days
	 * @return RFDate
	 */
	public function addDays($days)
	{
		// can also use DateTime->modify()
		return new RFDate($this->Format(self::SHORT_FORMAT) . " +" . $days . " days", $this->timezone);
	}

	/**
	 * @param int $months
	 * @return RFDate
	 */
	public function addMonths($months)
	{
		return new RFDate($this->Format(self::SHORT_FORMAT) . " +" . $months . " months", $this->timezone);
	}

	/**
	 * @param int $minutes
	 * @return RFDate
	 */
	public function addMinutes($minutes)
	{
		return new RFDate($this->Format(self::SHORT_FORMAT) . " +" . $minutes . " minutes", $this->timezone);
	}

	/**
	 * @param int $minutes
	 * @return RFDate
	 */
	public function removeMinutes($minutes)
	{
		return new RFDate($this->format(self::SHORT_FORMAT) . " -" . $minutes . " minutes", $this->timezone);
	}

	/**
	 * @param RFTime $time
	 * @param bool $isEndTime
	 * @return RFDate
	 */
	public function setTime(RFTime $time, $isEndTime = false)
	{
		$date = RFDate::create($this->year(), $this->month(), $this->day(), $time->hour(), $time->minute(),
							 $time->Second(), $this->timezone());

		if ($isEndTime)
		{
			if ($time->hour() == 0 && $time->minute() == 0 && $time->Second() == 0)
			{
				return $date->addDays(1);
			}
		}

		return $date;
	}

	/**
	 * @param string $time
	 * @param bool $isEndTime
	 * @return RFDate
	 */
	public function setTimeString($time, $isEndTime = false)
	{
		return $this->setTime(RFTime::parse($time, $this->timezone()), $isEndTime);
	}

	/**
	 * @param RFDate $date
	 * @return RFDateDiff
	 */
	public function getDifference(RFDate $date)
	{
		return RFDateDiff::betweenDates($this, $date);
	}

	/**
	 * @param RFDateDiff $difference
	 * @return RFDate
	 */
	public function applyDifference(RFDateDiff $difference)
	{
		if ($difference->isNull())
		{
			return $this->copy();
		}

		$newTimestamp = $this->timestamp() + $difference->totalSeconds();
		$dateStr = gmdate(self::SHORT_FORMAT, $newTimestamp);
		$date = new DateTime($dateStr, new DateTimeZone('UTC'));
		$date->setTimezone(new DateTimeZone($this->timezone()));

		return new RFDate($date->format(self::SHORT_FORMAT), $this->timezone());
	}

	private function initParts()
	{
		list($date, $time) = explode(' ', $this->format('w-' . self::SHORT_FORMAT));
		list($weekday, $year, $month, $day) = explode("-", $date);
		list($hour, $minute, $second) = explode(":", $time);

		$this->parts['hours'] = intval($hour);
		$this->parts['minutes'] = intval($minute);
		$this->parts['seconds'] = intval($second);
		$this->parts['mon'] = intval($month);
		$this->parts['mday'] = intval($day);
		$this->parts['year'] = intval($year);
		$this->parts['wday'] = intval($weekday);
	}

	/**
	 * @return int
	 */
	public function hour()
	{
		return $this->parts['hours'];
	}

	/**
	 * @return int
	 */
	public function minute()
	{
		return $this->parts['minutes'];
	}

	/**
	 * @return int
	 */
	public function second()
	{
		return $this->parts['seconds'];
	}

	/**
	 * @return int
	 */
	public function month()
	{
		return $this->parts['mon'];
	}

	/**
	 * @return int
	 */
	public function day()
	{
		return $this->parts['mday'];
	}

	/**
	 * @return int
	 */
	public function year()
	{
		return $this->parts['year'];
	}

	/**
	 * @return int
	 */
	public function weekday()
	{
		return $this->parts['wday'];
	}

	public function timezone()
	{
		return $this->timezone;
	}

	/**
	 * Only used for unit testing
	 * @param RFDate $date
	 */
	public static function _setNow(RFDate $date)
	{
		if (is_null($date))
		{
			self::$_Now = null;
		}
		else
		{
			self::$_Now = $date;
		}
	}

	/**
	 * Only used for unit testing
	 */
	public static function _resetNow()
	{
		self::$_Now = null;
	}

	public function toString()
	{
		return $this->format('Y-m-d H:i:s') . ' ' . $this->timezone;
	}

	public function __toString()
	{
		return $this->toString();
	}

	/**
	 * @static
	 * @return RFDate
	 */
	public static function min()
	{
		return RFDate::parse('0001-01-01', 'UTC');
	}

	/**
	 * @static
	 * @return RFDate
	 */
	public static function max()
	{
		return RFDate::parse('9999-01-01', 'UTC');
	}

	/**
	 * @return RFDate
	 */
	public function toTheMinute()
	{
		$time = $this->getTime();
		return $this->setTime(new RFTime($time->hour(), $time->minute(), 0, $this->timezone()));
	}
}
