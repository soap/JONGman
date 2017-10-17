<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

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