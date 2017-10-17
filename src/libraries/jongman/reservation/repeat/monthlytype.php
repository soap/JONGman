<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRepeatMonthlytype
{
	const DayOfMonth = 'dayOfMonth';
	const DayOfWeek = 'dayOfWeek';

	/**
	 * @param string $value
	 * @return bool
	 */
	public static function isDefined($value)
	{
		switch ($value)
		{
			case self::DayOfMonth:
			case self::DayOfWeek:
				return true;
			default:
				return false;
		}
	}
}