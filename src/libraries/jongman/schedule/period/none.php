<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFSchedulePeriodNone extends RFSchedulePeriod
{
	public function isReservable()
	{
		return false;
	}

	public function toUtc()
	{
		return new RFSchedulePeriodNone($this->_begin->toUtc(), $this->_end->toUtc(), $this->_label);
	}

	public function toTimezone($timezone)
	{
		return new RFSchedulePeriodNone($this->_begin->toTimezone($timezone), $this->_end->toTimezone($timezone), $this->_label);
	}
}
