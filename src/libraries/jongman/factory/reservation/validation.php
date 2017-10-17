<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFFactoryReservationValidation implements IReservationValidationFactory
{
	/**
	 * @var array|string[]
	 */
	private $creationStrategies = array();

	public function __construct()
	{
		$this->creationStrategies[RFReservationAction::Approve] = 'createUpdateService';
		$this->creationStrategies[RFReservationAction::Create] = 'createAddService';
		$this->creationStrategies[RFReservationAction::Delete] = 'createDeleteService';
		$this->creationStrategies[RFReservationAction::Update] = 'createUpdateService';
	}

	public function create($reservationAction, $user)
	{
		if (array_key_exists($reservationAction, $this->creationStrategies))
		{
			$createMethod = $this->creationStrategies[$reservationAction];
			return $this->$createMethod($user);
		}

		return new RFNullReservationValidationService();
	}

	private function createAddService($user)
	{
		//$factory = PluginManager::Instance()->LoadPreReservation();
		$factory = new RFFactoryPreReservation();
		return $factory->createPreAddService($user);
	}

	private function createUpdateService($user)
	{
		//$factory = PluginManager::Instance()->LoadPreReservation();
		$factory = new RFFactoryPreReservation();
		return $factory->createPreUpdateService($user);
	}

	private function createDeleteService($user)
	{
		//$factory = PluginManager::Instance()->LoadPreReservation();
		$factory = new RFFactoryPreReservation();
		return $factory->createPreDeleteService($user);
	}
}

class RFNullReservationValidationService implements IReservationValidationService
{
	/**
	 * @param ReservationSeries $reservation
	 * @return IReservationValidationResult
	 */
	function validate($reservation)
	{
		return new RFReservationValidationResult();
	}
}