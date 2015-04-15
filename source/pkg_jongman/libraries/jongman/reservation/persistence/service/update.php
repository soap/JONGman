<?php
defined('_JEXEC') or die;

interface IUpdateReservationPersistenceService extends IReservationPersistenceService
{
	/**
	 * @param int $reservationInstanceId
	 * @return RFReservationExistingSeries
	 */
	public function loadByInstanceId($reservationInstanceId);

	/**
	 * @param string $referenceNumber
	 * @return RFReservationExistingSeries
	*/
	public function loadByReferenceNumber($referenceNumber);
}

class RFReservationPersistenceServiceUpdate implements IUpdateReservationPersistenceService
{
	/**
	 * @var IReservationRepository
	 */
	private $_repository;

	public function __construct(IReservationRepository $repository)
	{
		$this->_repository = $repository;
	}

	public function LoadByInstanceId($reservationInstanceId)
	{
		return $this->_repository->loadById($reservationInstanceId);
	}

	public function persist($existingReservationSeries)
	{
		$this->_repository->update($existingReservationSeries);
	}

	public function LoadByReferenceNumber($referenceNumber)
	{
		return $this->_repository->loadByReferenceNumber($referenceNumber);
	}
}