<?php

interface IDeleteReservationPersistenceService extends IReservationPersistenceService
{
	/**
	 * @param string $referenceNumber
	 * @return ExistingReservationSeries
	 */
	public function loadByReferenceNumber($referenceNumber);
}

class RFReservationServicePersistenceDelete implements IDeleteReservationPersistenceService
{
	/**
	 * @var IReservationRepository
	 */
	private $_repository;

	public function __construct(IReservationRepository $repository)
	{
		$this->_repository = $repository;
	}

	public function loadByReferenceNumber($referenceNumber)
	{
		return $this->_repository->loadByReferenceNumber($referenceNumber);
	}

	public function Persist($existingReservationSeries)
	{
		$this->_repository->delete($existingReservationSeries);
	}
}

?>