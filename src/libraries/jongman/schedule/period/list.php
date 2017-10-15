<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFSchedulePeriodList
{
	private $items = array();
	private $_addedStarts = array();
	private $_addedTimes = array();
	private $_addedEnds = array();

	public function add(RFSchedulePeriod $period)
	{
		if (!$period->IsReservable())
		{
			//TODO: Config option to hide non-reservable periods
		}

		if ($this->alreadyAdded($period->beginDate(), $period->endDate()))
		{
			//echo "already added $period\n";
			return;
		}

		//echo "\nadding {$period->BeginDate()} - {$period->EndDate()}";
		$this->items[] = $period;
	}

	public function getItems()
	{
		return $this->items;
	}

	private function alreadyAdded(RFDate $start, RFDate $end)
	{
		$startExists = false;
		$endExists = false;

		if (array_key_exists($start->timestamp(), $this->_addedStarts))
		{
			$startExists = true;
		}

		if (array_key_exists($end->timestamp(), $this->_addedEnds))
		{
			$endExists = true;
		}

		$this->_addedTimes[$start->timestamp()] = true;
		$this->_addedEnds[$end->timestamp()] = true;

		return $startExists || $endExists;
	}
}
