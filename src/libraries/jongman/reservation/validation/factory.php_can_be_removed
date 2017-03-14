<?php
class RFReservationValidationFactory implements IReservationValidationFactory
{
	/**
	 * @var array|string[]
	 */
	private $creationStrategies = array();

	public function __construct()
	{
		$this->creationStrategies[ReservationAction::Create] = 'CreateAddService';
		$this->creationStrategies[ReservationAction::Delete] = 'CreateDeleteService';
		$this->creationStrategies[ReservationAction::Update] = 'CreateUpdateService';
	}

	public function create($reservationAction, $userSession)
	{
		if (array_key_exists($reservationAction, $this->creationStrategies))
		{
			$createMethod = $this->creationStrategies[$reservationAction];
			return $this->$createMethod($userSession);
		}

		return new RFNullReservationValidationService();
	}

	private function createAddService(UserSession $userSession)
	{
		//$factory = PluginManager::Instance()->LoadPreReservation();
		return $factory->createPreAddService($userSession);
	}

	private function CreateUpdateService(UserSession $userSession)
	{
		//$factory = PluginManager::Instance()->LoadPreReservation();
		return $factory->createPreUpdateService($userSession);
	}

	private function createDeleteService(UserSession $userSession)
	{
		//$factory = PluginManager::Instance()->LoadPreReservation();
		return $factory->createPreDeleteService($userSession);
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