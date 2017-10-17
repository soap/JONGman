<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
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
