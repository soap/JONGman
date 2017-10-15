<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('jongman.ireservationnotificationservice');

class RFFactoryReservationNotification implements IReservationNotificationFactory
{
	/**
	 * @var array|string[]
	 */
	private $creationStrategies = array();
	
	public function __construct()
	{
		$this->creationStrategies[RFReservationAction::Approve] = 'createApproveService';
		$this->creationStrategies[RFReservationAction::Create] = 'createAddService';
		$this->creationStrategies[RFReservationAction::Delete] = 'createDeleteService';
		$this->creationStrategies[RFReservationAction::Update] = 'createUpdateService';
	}
	
	public function create($reservationAction, $userSession)
	{
		if (array_key_exists($reservationAction, $this->creationStrategies))
		{
			$createMethod = $this->creationStrategies[$reservationAction];
			return $this->$createMethod($userSession);
		}
		
		return new RFReservationNotificationServiceNull();
	}

	private function createAddService($userSession)
	{
		$factory = new RFFactoryPostReservation();
		return $factory->createPostAddService($userSession);
	}
	
	private function createApproveService($userSession)
	{
		$factory = new RFFactoryPostReservation();
		return $factory->createPostApproveService($userSession);
	}
	
	private function createDeleteService($userSession)
	{
		$factory = new RFFactoryPostReservation();
		return $factory->createPostDeleteService($userSession);
	}
	
	private function createUpdateService($userSession)
	{
		$factory = new RFFactoryPostReservation();
		return $factory->createPostUpdateService($userSession);
	}	
}

class RFReservationNotificationServiceNull implements IReservationNotificationService
{
	/**
	 * @param ReservationSeries $reservationSeries
	 */
	function notify($reservationSeries)
	{
		// no-op
	}
}