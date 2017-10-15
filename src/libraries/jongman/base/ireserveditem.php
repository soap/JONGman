<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IReservedItem
{
	/**
	 * @abstract
	 * @return Date
	 */
	public function getStartDate();

	/**
	 * @abstract
	 * @return Date
	 */
	public function getEndDate();

	/**
	 * @abstract
	 * @return int
	 */
	public function getResourceId();

	/**
	 * @abstract
	 * @return int
	 */
	public function getId();

	/**
	 * @abstract
	 * @param Date $date
	 * @return bool
	 */
	public function occursOn(RFDate $date);
}
