<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IBlackoutValidationResult
{
	/**
	 * @return bool
	 */
	public function wasSuccessful();

	/**
	 * @abstract
	 * @return string
	*/
	public function message();

	/**
	 * @abstract
	 * @return array|RFReservationItemView[]
	*/
	public function conflictingReservations();

	/**
	 * @abstract
	 * @return array|RFBlackoutItemView[]
	*/
	public function conflictingBlackouts();
}