<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IReservationPersistenceFactory
{
	/**
	 * @param ReservationAction $reservationAction
	 * @return IReservationPersistenceService
	 */
	function create($reservationAction);
}
