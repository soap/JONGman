<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

interface IRepeatOptionsComposite
{
	/**
	 * @abstract
	 * @return string
	 */
	public function getRepeatType();

	/**
	 * @abstract
	 * @return string|null
	*/
	public function getRepeatInterval();

	/**
	 * @abstract
	 * @return int[]|null
	*/
	public function getRepeatWeekdays();

	/**
	 * @abstract
	 * @return string|null
	*/
	public function getRepeatMonthlyType();

	/**
	 * @abstract
	 * @return string|null
	*/
	public function getRepeatTerminationDate();
}