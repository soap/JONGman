<?php
defined('_JEXEC') or die;

class RFDayOfWeek
{
	const SUNDAY = 0;
	const MONDAY = 1;
	const TUESDAY = 2;
	const WEDNESDAY = 3;
	const THURSDAY = 4;
	const FRIDAY = 5;
	const SATURDAY = 6;

	const NumberOfDays = 7;

	/**
	 * @return array|int[]|DayOfWeek
	 */
	public static function days()
	{
		return range(self::SUNDAY, self::SATURDAY);
	}
}
