<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


interface ICalendarSegment
{
	/**
	 * @abstract
	 * @return RFDate
	 */
	public function firstDay();

	/**
	 * @abstract
	 * @return Date
	*/
	public function lastDay();

	/**
	 * @abstract
	 * @param $reservations array|RFCalendarReservation[]
	 * @return void
	*/
	public function addReservations($reservations);

	/**
	 * @abstract
	 * @return string|RFCalendarTypes
	*/
	public function getType();

	/**
	 * @abstract
	 * @return Date
	*/
	public function getPreviousDate();

	/**
	 * @abstract
	 * @return Date
	*/
	public function getNextDate();

	/**
	 * @abstract
	 * @return  array|RFCalendarReservation[]
	*/
	public function getReservations();
}