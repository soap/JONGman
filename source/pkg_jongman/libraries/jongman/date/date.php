<?php
defined('_JEXEC') or die;


class JMDate
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
			$user = JFactory::getUser();
			$tz = $user->getParam('timezone', JFactory::getConfig()->get('offset'));
			$this->timezone = $tz ;
		}
	}

	/**
	 * Creates a new Date object with the given year, month, day, and optional $hour, $minute, $secord and $timezone
	 * @return Date
	 */
	public static function create($year, $month, $day, $hour = 0, $minute = 0, $second = 0, $timezone = null)
	{
		if ($month > 12)
		{
			$yearOffset = floor($month / 12);
			$year = $year + $yearOffset;
			$month = $month - ($yearOffset * 12);
		}

		return new JMDate(sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute,
								$second), $timezone);
	}

	/**
	 * Creates a new Date object from the given string and $timezone
	 * @param string $dateString
	 * @param string|null $timezone
	 * @return Date
	 */
	public static function parse($dateString, $timezone = null)
	{
		if (empty($dateString))
		{
			return NullDate::Instance();
		}
		return new JMDate($dateString, $timezone);
	}

	/**
	 * @param string $dateString
	 * @return Date
	 */
	public static function parseExact($dateString)
	{
		if (empty($dateString))
		{
			return NullDate::Instance();
		}

		$date = new DateTime($dateString);
		$timeOffsetString = $date->getTimezone()->getName();
		$offsetParts = explode(':', $timeOffsetString);

		$d = new JMDate($date->format(Date::SHORT_FORMAT), 'UTC');
		$offsetMinutes = ($offsetParts[0] * -60) + $offsetParts[1];
		return $d->AddMinutes($offsetMinutes);
	}

	/**
	 * Returns a Date object representing the current date/time in the server's timezone
	 *
	 * @return Date
	 */
	public static function now()
	{
		if (isset(self::$_Now))
		{
			return self::$_Now;
		}

		return new JMDate('now');
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
	 * @return Date
	 */
	public function toTimezone($timezone)
	{
		if ($this->timezone() == $timezone)
		{
			return $this->copy();
		}

		$date = new DateTime($this->timestring, new DateTimeZone($this->timezone));

		$date->setTimezone(new DateTimeZone($timezone));
		$adjustedDate = $date->format(JMDate::SHORT_FORMAT);

		return new JMDate($adjustedDate, $timezone);
	}

	/**
	 * @return Date
	 */
	public function copy()
	{
		return new JMDate($this->timestring, $this->timezone());
	}

	/**
	 * Returns the Date adjusted into UTC
	 *
	 * @return Date
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
	 *
	 * @return string
	 */
	public function toDatabase()
	{
		return $this->toUtc()->Format('Y-m-d H:i:s');
	}

	/**
	 * @param string $databaseValue
	 * @return Date
	 */
	public static function fromDatabase($databaseValue)
	{
		if (empty($databaseValue))
		{
			return NullDate::getInstance();
		}
		return JMDate::parse($databaseValue, 'UTC');
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
	 * @return Time
	 */
	public function getTime()
	{
		return new Time($this->hour(), $this->minute(), $this->second(), $this->timezone());
	}

	/**
	 * Returns the Date only part of the date.  Hours, Minutes and Seconds will be 0
	 *
	 * @return Date
	 */
	public function getDate()
	{
		return JMDate::create($this->year(), $this->month(), $this->day(), 0, 0, 0, $this->timezone());
	}

	/**
	 * Compares this date to the one passed in
	 * Returns:
	 * -1 if this date is less than the passed in date
	 * 0 if the dates are equal
	 * 1 if this date is greater than the passed in date
	 * @param Date $date
	 * @return int comparison result
	 */
	public function compare(JMDate $date)
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
	 * @param Date $date
	 * @return int comparison result
	 */
	public function compareTime(JMDate $date)
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
	 * @param Date $end
	 * @return bool if the current object is greater than the one passed in
	 */
	public function greaterThan(JMDate $end)
	{
		return $this->compare($end) > 0;
	}

	/**
	 * Compares this date to the one passed in
	 * @param Date $end
	 * @return bool if the current object is less than the one passed in
	 */
	public function lessThan(JMDate $end)
	{
		return $this->compare($end) < 0;
	}

	/**
	 * Compare the 2 dates
	 *
	 * @param Date $date
	 * @return bool
	 */
	public function equals(JMDate $date)
	{
		return $this->compare($date) == 0;
	}

	/**
	 * @param Date $date
	 * @return bool
	 */
	public function dateEquals(JMDate $date)
	{
		$date2 = $date;
		if ($date2->timezone() != $this->timezone())
		{
			$date2 = $date->toTimezone($this->timezone);
		}

		return ($this->day() == $date2->day() && $this->month() == $date2->month() && $this->year() == $date2->year());
	}

	public function dateCompare(Date $date)
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
	 * @return Date
	 */
	public function addDays($days)
	{
		// can also use DateTime->modify()
		return new JMDate($this->Format(self::SHORT_FORMAT) . " +" . $days . " days", $this->timezone);
	}

	/**
	 * @param int $months
	 * @return Date
	 */
	public function addMonths($months)
	{
		return new JMDate($this->Format(self::SHORT_FORMAT) . " +" . $months . " months", $this->timezone);
	}

	/**
	 * @param int $minutes
	 * @return Date
	 */
	public function addMinutes($minutes)
	{
		return new JMDate($this->Format(self::SHORT_FORMAT) . " +" . $minutes . " minutes", $this->timezone);
	}

	/**
	 * @param int $minutes
	 * @return Date
	 */
	public function removeMinutes($minutes)
	{
		return new JMDate($this->format(self::SHORT_FORMAT) . " -" . $minutes . " minutes", $this->timezone);
	}

	/**
	 * @param Time $time
	 * @param bool $isEndTime
	 * @return Date
	 */
	public function setTime(Time $time, $isEndTime = false)
	{
		$date = JMDate::create($this->year(), $this->month(), $this->day(), $time->hour(), $time->minute(),
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
	 * @return Date
	 */
	public function setTimeString($time, $isEndTime = false)
	{
		return $this->setTime(Time::parse($time, $this->timezone()), $isEndTime);
	}

	/**
	 * @param Date $date
	 * @return DateDiff
	 */
	public function getDifference(JMDate $date)
	{
		return DateDiff::betweenDates($this, $date);
	}

	/**
	 * @param DateDiff $difference
	 * @return Date
	 */
	public function applyDifference(DateDiff $difference)
	{
		if ($difference->isNull())
		{
			return $this->copy();
		}

		$newTimestamp = $this->timestamp() + $difference->totalSeconds();
		$dateStr = gmdate(self::SHORT_FORMAT, $newTimestamp);
		$date = new DateTime($dateStr, new DateTimeZone('UTC'));
		$date->setTimezone(new DateTimeZone($this->timezone()));

		return new JMDate($date->format(self::SHORT_FORMAT), $this->timezone());
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
	 * @param Date $date
	 */
	public static function _setNow(JMDate $date)
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
		return $this->ToString();
	}

	/**
	 * @static
	 * @return Date
	 */
	public static function min()
	{
		return Date::Parse('0001-01-01', 'UTC');
	}

	/**
	 * @static
	 * @return Date
	 */
	public static function max()
	{
		return Date::Parse('9999-01-01', 'UTC');
	}

	/**
	 * @return Date
	 */
	public function toTheMinute()
	{
		$time = $this->GetTime();
		return $this->setTime(new Time($time->hour(), $time->minute(), 0, $this->timezone()));
	}
}

class NullDate extends JMDate
{
	/**
	 * @var NullDate
	 */
	private static $ndate;

	public function __construct()
	{
		//parent::__construct();
	}

	public static function getInstance()
	{
		if (self::$ndate == null)
		{
			self::$ndate = new NullDate();
		}

		return self::$ndate;
	}

	public function format($format)
	{
		return '';
	}

	public function toString()
	{
		return '';
	}

	public function toDatabase()
	{
		return null;
	}

	public function toTimezone($timezone)
	{
		return $this;
	}
}

class DateDiff
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
	public static function betweenDates(JMDate $date1, JMDate $date2)
	{
		if ($date1->equals($date2))
		{
			return DateDiff::null();
		}

		$compareDate = $date2;
		if ($date1->timezone() != $date2->timezone())
		{
			$compareDate = $date2->toTimezone($date1->timezone());
		}

		return new DateDiff($compareDate->timestamp() - $date1->timestamp());
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
		return new DateDiff(0);
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
	public function add(Datediff $diff)
	{
		return new DateDiff($this->seconds + $diff->seconds);
	}

	/**
	 * @param DateDiff $diff
	 * @return bool
	 */
	public function greaterThan(DateDiff $diff)
	{
		return $this->seconds > $diff->seconds;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		$str = '';

		if ($this->Days() > 0)
		{
			$str .= $this->Days() . ' days ';
		}
		if ($this->Hours() > 0)
		{
			$str .= $this->Hours() . ' hours ';
		}
		if ($this->Minutes() > 0)
		{
			$str .= $this->Minutes() . ' minutes';
		}

		return $str;
		//return sprintf('%dd%dh%dm', $this->Days(), $this->Hours(), $this->Minutes());
	}
}

?>