<?php
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