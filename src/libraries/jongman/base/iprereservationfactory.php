<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IPreReservationFactory
{
	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	public function createPreAddService(JUser $user);

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	*/
	public function createPreUpdateService(JUser $user);

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	*/
	public function createPreDeleteService(JUser $user);
}


