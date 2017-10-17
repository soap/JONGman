<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFLayoutPeriod
{
	/**
	 * @var Time
	 */
	public $start;

	/**
	 * @var Time
	 */
	public $end;

	/**
	 * @var PeriodTypes
	 */
	public $periodType;

	/**
	 * @var string
	 */
	public $label;

	/**
	 * @return string
	 */
	public function periodTypeClass()
	{
		if ($this->periodType == RFSchedulePeriodTypes::RESERVABLE)
		{
			return 'RFSchedulePeriod';
		}

		return 'RFSchedulePeriodNone';
	}

	/**
	 * @return bool
	 */
	public function isReservable()
	{
		return $this->periodType == RFSchedulePeriodTypes::RESERVABLE;
	}

	/**
	 * @return bool
	 */
	public function isLabeled()
	{
		return !empty($this->label);
	}

	/**
	 * @return string
	 */
	public function timezone()
	{
		return $this->start->timezone();
	}

	public function __construct(RFTime $start, RFTime $end, $periodType = PeriodTypes::RESERVABLE, $label = null)
	{
		$this->start = $start;
		$this->end = $end;
		$this->periodType = $periodType;
		$this->label = $label;
	}

	/**
	 * Compares the starting times
	 */
	public function compare(RFLayoutPeriod $other)
	{
		return $this->start->compare($other->start);
	}
}