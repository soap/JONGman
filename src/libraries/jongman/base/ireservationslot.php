<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
Copyright 2011-2013 Nick Korbel

This file is part of phpScheduleIt.

phpScheduleIt is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

phpScheduleIt is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with phpScheduleIt.  If not, see <http://www.gnu.org/licenses/>.
*/

interface IReservationSlot
{
	/**
	 * @return Time
	 */
	public function begin();
	
	/**
	 * @return Time
	 */
	public function end();
	
	/**
	 * @return Date
	 */
	public function beginDate();
	
	/**
	 * @return Date
	 */
	public function endDate();
	
	/**
	 * @return Date
	 */
	public function date();
	
	/**
	 * @return int
	 *
	 */
	public function periodSpan();	
	
	/**
	 * @return string
	 */
	public function label();
	
	/**
	 * @return bool
	 */
	public function isReservable();
	
	/**
	 * @return bool
	 */
	public function isReserved();

	/**
	 * @return bool
	 */
	public function isPending();
	
	/**
	 * @param $date Date
	 * @return bool
	 */
	public function isPastDate(RFDate $date);
	
	/**
	 * @param string $timezone
	 * @return IReservationSlot
	 */
	public function toTimezone($timezone);

	/**
	 * @param JUser $user
	 * @return bool
	 */
	public function isOwnedBy(JUser $user);

	/**
	 * @param JUser $user
	 * @return bool
	 */
	public function isParticipating(JUser $user);

	/**
	 * @return string
	 */
	public function beginSlotId();

	/**
	 * @return string
	 */
	public function endSlotId();
}
