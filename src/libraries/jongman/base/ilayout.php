<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface ILayoutTimezone
{
	public function timezone();
}

interface ILayoutDailySchedule
{
	/**
	 * @return bool
	 */
	public function usesDailyLayouts();
}

interface ILayoutSchedule extends ILayoutTimezone, ILayoutDailySchedule
{
	/**
	 * @param Date $layoutDate
	 * @param bool $hideBlockedPeriods
	 * @return SchedulePeriod[]|array of SchedulePeriod objects
	 */
	public function getLayout(RFDate $layoutDate, $hideBlockedPeriods = false);

	/**
	 * @abstract
	 * @param Date $date
	 * @return SchedulePeriod|null period which occurs at this datetime. Includes start time, excludes end time. null if no match is found
	 */
	public function getPeriod(RFDate $date);
}

interface ILayoutCreation extends ILayoutTimezone, ILayoutDailySchedule
{
	/**
	 * Appends a period to the schedule layout
	 *
	 * @param Time $startTime starting time of the schedule in specified timezone
	 * @param Time $endTime ending time of the schedule in specified timezone
	 * @param string $label optional label for the period
	 * @param DayOfWeek|int|null $dayOfWeek
	 */
	function appendPeriod(RFTime $startTime, RFTime $endTime, $label = null, $dayOfWeek = null);

	/**
	 * Appends a period that is not reservable to the schedule layout
	 *
	 * @param Time $startTime starting time of the schedule in specified timezone
	 * @param Time $endTime ending time of the schedule in specified timezone
	 * @param string $label optional label for the period
	 * @param DayOfWeek|int|null $dayOfWeek
	 * @return void
	 */
	function appendBlockedPeriod(RFTime $startTime, RFTime $endTime, $label = null, $dayOfWeek = null);

	/**
	 *
	 * @param DayOfWeek|int|null $dayOfWeek
	 * @return LayoutPeriod[] array of LayoutPeriod
	 */
	function getSlots($dayOfWeek = null);
}
