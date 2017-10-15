<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface ICalendarFactory
{
	/**
	 * @abstract
	 * @param $type
	 * @param $year
	 * @param $month
	 * @param $day
	 * @param $timezone
	 * @param $firstDayOfWeek
	 * @return ICalendarSegment
	 */
	public function create($type, $year, $month, $day, $timezone, $firstDayOfWeek = 0);
}

class RFFactoryCalendar implements ICalendarFactory
{
	public function create($type, $year, $month, $day, $timezone, $firstDayOfWeek = 0)
	{
		if ($type == RFCalendarTypes::Day)
		{
			return new RFCalendarDay(RFDate::create($year, $month, $day, 0, 0, 0, $timezone));
		}

		if ($type == RFCalendarTypes::Week)
		{
			return RFCalendarWeek::fromDate($year, $month, $day, $timezone, $firstDayOfWeek);
		}

		return new RFCalendarMonth($month, $year, $timezone);
	}
}