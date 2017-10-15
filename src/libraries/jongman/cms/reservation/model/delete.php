<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

/**
 * Reservation Delete Model class to interface with CMS Model like Joomla
 * @author Prasit Gebsaap
 *
 */
class RFReservationModelDelete implements IReservationDeleteModel
{
	/**
	 * @var IReservationPage
	 */
	private $page;

	/**
	 * @var IDeleteReservationPersistenceService
	 */
	private $persistenceService;

	/**
	 * @var IReservationHandler
	 */
	private $handler;

	/**
	 * @var UserSession
	 */
	private $user;
	/**
	 * 
	 * @param IReservationPage $cmsModel
	 * @param IDeleteReservationPersistenceService $persistenceService
	 * @param IReservationHandler $handler
	 * @param UserSession $userSession
	 */
	public function __construct(
			IReservationPage $cmsModel,
			IDeleteReservationPersistenceService $persistenceService,
			IReservationHandler $handler,
			$user)
	{
		$this->page = $cmsModel;
		$this->persistenceService = $persistenceService;
		$this->handler = $handler;
		$this->user = $user;
	}

	/**
	 * @return RFExistingReservationSeries
	 */
	public function buildReservation()
	{
		// $this->page is Joomla model
		$referenceNumber = $this->page->getReferenceNumber();
		$existingSeries = $this->persistenceService->loadByReferenceNumber($referenceNumber);
		$existingSeries->applyChangesTo($this->page->getSeriesUpdateScope());

		$existingSeries->delete($this->user);

		return $existingSeries;
	}

	/**
	 * @param ExistingReservationSeries $reservationSeries
	 */
	public function handleReservation($reservationSeries)
	{
		JLog::add(JText::sprintf('COM_JONGMAN_RESERVATION_DELETED'), $reservationSeries->currentInstance()->referenceNumber());

		$this->handler->handle($reservationSeries, $this->page);
	}
}

interface IReservationDeleteModel
{
	/**
	 * @return RFExistingReservationSeries
	 */
	public function buildReservation();

	/**
	 * @param RFExistingReservationSeries $reservationSeries
	*/
	public function handleReservation($reservationSeries);
}