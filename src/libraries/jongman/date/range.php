<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
You should have received a copy of the GNU General Public License
along with phpScheduleIt.  If not, see <http://www.gnu.org/licenses/>.
*/

class RFDateRange
{
	/**
	 * @var RFDate
	 */
	private $_begin;
	
	/**
	 * @var RFDate
	 */
	private $_end;

	/**
	 * @var string
	 */
	private $_timezone;

	/**
	 * @param RFDate $begin
	 * @param RFDate $end
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
	 * @return RFDateRange
	 */
	public static function create($beginString, $endString, $timezoneString)
	{
		return new RFDateRange(RFDate::parse($beginString, $timezoneString), RFDate::parse($endString, $timezoneString), $timezoneString);
	}

	/**
	 * Whether or not the $date is within the range.  Range boundaries are inclusive
	 * @param RFDate $date
	 * @return bool
	 */
	public function contains(RFDate $date)
	{
		return $this->_begin->compare($date) <= 0 && $this->_end->compare($date) >= 0;
	}

	/**
	 * @param RFDateRange $dateRange
	 * @return bool
	 */
	public function containsRange(RFDateRange $dateRange)
	{
		return $this->_begin->compare($dateRange->_begin) <= 0 && $this->_end->compare($dateRange->_end) >= 0;
	}

	/**
	 * Whether or not the date ranges overlap.  Dates that start or end on boundaries are excluded
	 * @param RFDateRange $dateRange
	 * @return bool
	 */
	public function overlaps(RFDateRange $dateRange)
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
	 * @param RFDate $date
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
	 * @return RFDate
	 */
	public function getBegin()
	{
		return $this->_begin;	
	}

	/**
	 * @return RFDate
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
	 * @param RFDateRange $otherRange
	 * @return bool
	 */
	public function equals(RFDateRange $otherRange)
	{
		return $this->_begin->equals($otherRange->getBegin()) && $this->_end->equals($otherRange->getEnd());
	}
	
	/**
	 * @param string $timezone
	 * @return RFDateRange
	 */
	public function toTimezone($timezone)
	{
		return new RFDateRange($this->_begin->toTimezone($timezone), $this->_end->toTimezone($timezone));
	}
	
	/**
	 * @return RFDateRange
	 */
	public function toUtc()
	{
		return new RFDateRange($this->_begin->toUtc(), $this->_end->toUtc());
	}
	
	/**
	 * @param int $days
	 * @return RFDateRange
	 */
	public function addDays($days)
	{
		return new RFDateRange($this->_begin->addDays($days), $this->_end->addDays($days));
	}
	
	/**
	 * @return string
	 */
	public function toString()
	{
		return "\nBegin: " . $this->_begin->toString() . " End: " . $this->_end->toString() . "\n";
	}

    /**
     * @return string
     */
    public function __toString()
	{
		return $this->toString();
	}
}
