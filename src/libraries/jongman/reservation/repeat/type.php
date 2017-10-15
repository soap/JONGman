<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRepeatType
{
	const NONE = 'none';
	const DAILY = 'daily';
	const WEEKLY = 'weekly';
	const MONTHLY = 'monthly';
	const YEARLY = 'yearly';

	/**
	 * @param string $value
	 * @return bool
	 */
	public static function isDefined($value)
	{
		switch ($value)
		{
			case self::NONE:
			case self::DAILY:
			case self::WEEKLY:
			case self::MONTHLY:
			case self::YEARLY;
				return true;
			default:
				return false;

		}
	}
}