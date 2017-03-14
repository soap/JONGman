<?php
defined('_JEXEC') or die;

class RFReservationPersistenceServiceAdd implements IReservationPersistenceService
{
	/**
	 * @var IReservationRepository
	 */
	private $_repository;

	public function __construct(IReservationRepository $repository)
	{
		$this->_repository = $repository;
	}

	public function persist($reservation)
	{
		$this->_repository->add($reservation);
	}
}