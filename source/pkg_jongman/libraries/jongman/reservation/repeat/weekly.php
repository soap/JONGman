<?php
defined('_JEXEC') or die;


class RFReservationRepeatWeekly extends RFReservationRepeatAbstract
{
	/**
	 * @var array
	 */
	private $_daysOfWeek = array();

	/**
	 * @param int $interval
	 * @param Date $terminationDate
	 * @param array $daysOfWeek
	 */
	public function __construct($interval, $terminationDate, $daysOfWeek)
	{
		parent::__construct($interval, $terminationDate);

		if ($daysOfWeek == null)
		{
			$daysOfWeek = array();
		}
		
		$this->_daysOfWeek = $daysOfWeek;
		
		if ($this->_daysOfWeek != null)
		{
			sort($this->_daysOfWeek);
		}
	}

	public function getDates(RFDateRange $startingRange)
	{
		if (empty($this->_daysOfWeek))
		{
			// use begin date 's weekday
			$this->_daysOfWeek = array($startingRange->getBegin()->weekday());
		}

		$dates = array();

		$startDate = $startingRange->getBegin();
		$endDate = $startingRange->getEnd();

		$startWeekday = $startDate->weekday();
		foreach ($this->_daysOfWeek as $weekday)
		{
			if ($startWeekday < $weekday)
			{
				$start = $startDate->addDays($weekday - $startWeekday);
				$end = $endDate->addDays($weekday - $startWeekday);

				$dates[] = new RFDateRange($start->toUtc(), $end->toUtc());
			}
		}

		$rawStart = $startingRange->getBegin();
		$rawEnd = $startingRange->getEnd();

		$week = 1;

		while ($startDate->dateCompare($this->_terminationDate) <= 0)
		{
			$weekOffset = (7 * $this->_interval * $week);

			for ($day = 0; $day < count($this->_daysOfWeek); $day++)
			{
				$intervalOffset = $weekOffset + ($this->_daysOfWeek[$day] - $startWeekday);
				$startDate = $rawStart->addDays($intervalOffset);
				$endDate = $rawEnd->addDays($intervalOffset);

				if ($startDate->dateCompare($this->_terminationDate) <= 0)
				{
					$dates[] = new RFDateRange($startDate->toUtc(), $endDate->toUtc());
				}
			}

			$week++;
		}

		return $dates;
	}

	public function repeatType()
	{
		return RFReservationRepeatType::WEEKLY;
	}

	public function configurationString()
	{
		$config = parent::ConfigurationString();
		return sprintf("%s|days=%s", $config, implode(',', $this->_daysOfWeek));
	}

	public function hasSameConfigurationAs(IRepeatOptions $repeatOptions)
	{
		return parent::hasSameConfigurationAs($repeatOptions) && $this->_daysOfWeek == $repeatOptions->_daysOfWeek;
	}
}
