<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class AdminSlotLabelFactory extends SlotLabelFactory
{
	protected function getFullName(ReservationItemView $reservation)
	{
		$name = new FullName($reservation->FirstName, $reservation->LastName);
		return $name->__toString();
	}
}