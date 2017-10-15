<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFReservationRepeatNone implements IRepeatOptions
{
	public function getDates(RFDateRange $startingDate)
	{
		return array();
	}

	public function repeatType()
	{
		return RFReservationRepeatType::NONE;
	}

	public function configurationString()
	{
		return '';
	}

	public function equals(IRepeatOptions $repeatOptions)
	{
		return get_class($this) == get_class($repeatOptions);
	}

	public function hasSameConfigurationAs(IRepeatOptions $repeatOptions)
	{
		return $this->equals($repeatOptions);
	}

	public function terminationDate()
	{
		return RFDate::now();
	}
}