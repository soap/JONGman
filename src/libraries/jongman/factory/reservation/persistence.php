<?php
defined('_JEXEC') or die;

jimport('jongman.reservation.persistence.service.add');
jimport('jongman.reservation.persistence.service.update');
jimport('jongman.reservation.persistence.service.delete');
class RFFactoryReservationPersistence implements IReservationPersistenceFactory
{
	private $services = array();
	private $creationStrategies = array();

	public function __construct()
	{
		$this->creationStrategies[RFReservationAction::Approve] = 'createUpdateService';
		$this->creationStrategies[RFReservationAction::Create] = 'createAddService';
		$this->creationStrategies[RFReservationAction::Delete] = 'createDeleteService';
		$this->creationStrategies[RFReservationAction::Update] = 'createUpdateService';
	}

	/**
	 * @param string $reservationAction
	 * @return IReservationPersistenceService
	 */
	public function create($reservationAction)
	{
		if (!array_key_exists($reservationAction, $this->services))
		{
			$this->addCachedService($reservationAction);
		}

		return $this->services[$reservationAction];
	}

	private function addCachedService($reservationAction)
	{
		$createMethod = $this->creationStrategies[$reservationAction];
		$this->services[$reservationAction] = $this->$createMethod();
	}

	private function createAddService()
	{
		return new RFReservationPersistenceServiceAdd(new RFReservationRepository());
	}

	private function createDeleteService()
	{
		return new RFReservationPersistenceServiceDelete(new RFReservationRepository());
	}

	private function createUpdateService()
	{
		return new RFReservationPersistenceServiceUpdate(new RFReservationRepository());
	}
}
?>