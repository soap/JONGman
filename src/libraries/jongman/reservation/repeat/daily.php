<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRepeatDaily extends RFReservationRepeatAbstract
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
		$startDate = $startingRange->getBegin()->addDays($this->_interval);
		$endDate = $startingRange->getEnd()->addDays($this->_interval);

		while ($startDate->dateCompare($this->_terminationDate) <= 0)
		{
			$dates[] = new RFDateRange($startDate->toUtc(), $endDate->toUtc());
			$startDate = $startDate->addDays($this->_interval);
			$endDate = $endDate->addDays($this->_interval);
		}

		return $dates;
	}

	public function repeatType()
	{
		return RFReservationRepeatType::DAILY;
	}	
}