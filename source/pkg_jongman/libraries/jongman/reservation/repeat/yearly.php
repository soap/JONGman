<?php
defined('_JEXEC') or die;

class RFReservationRepeatYearly extends RFReservationRepeatAbtract
{
	/**
	 * @param int $interval
	 * @param Date $terminationDate
	 */
	public function __construct($interval, $terminationDate)
	{
		parent::__construct($interval, $terminationDate);
	}

	public function getDates(RFDateRange $startingRange)
	{
		$dates = array();
		$begin = $startingRange->getBegin();
		$end = $startingRange->getEnd();

		$nextStartYear = $begin->year();
		$nextEndYear = $end->year();
		$timezone = $begin->timezone();

		$startDate = $begin;

		while ($startDate->dateCompare($this->_terminationDate) <= 0)
		{
			$nextStartYear = $nextStartYear + $this->_interval;
			$nextEndYear = $nextEndYear + $this->_interval;

			$startDate = RFDate::create($nextStartYear, $begin->month(), $begin->day(), $begin->hour(), $begin->minute(),
									  $begin->second(), $timezone);
			$endDate = RFDate::create($nextEndYear, $end->month(), $end->day(), $end->hour(), $end->minute(),
									$end->second(), $timezone);

			if ($startDate->dateCompare($this->_terminationDate) <= 0)
			{
				$dates[] = new RFDateRange($startDate->toUtc(), $endDate->toUtc());
			}
		}

		return $dates;
	}

	public function repeatType()
	{
		return RFRepeatType::YEARLY;
	}	
}
