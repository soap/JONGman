<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationReminderNull extends RFReservationReminder
{
	public function __construct()
	{
		parent::__construct(0, null);
	}

	public function enabled()
	{
		return false;
	}

	public function minutesPrior()
	{
		return 0;
	}
}