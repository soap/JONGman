<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFLayoutReservation extends RFLayoutSchedule implements ILayoutSchedule
{
	protected function spansMidnight(RFDate $start, RFDate $end)
	{
		return false;
	}
}