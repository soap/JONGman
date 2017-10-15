<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IReservationHandler
{
	/**
	 * @param RFReservationSeries $reservationSeries
	 * @param IReservationSaveResultView $view
	 * @return bool if the reservation was handled or not
	 */
	public function handle(RFReservationSeries $reservationSeries, IReservationSaveResultView $view);
}

class RFReservationHandler implements IReservationHandler
{
	/**
	 * @var IReservationPersistenceService
	 */
	private $persistenceService;

	/**
	 * @var IReservationValidationService
	 */
	private $validationService;

	/**
	 * @var IReservationNotificationService
	 */
	private $notificationService;

	public function __construct(IReservationPersistenceService $persistenceService, IReservationValidationService $validationService, IReservationNotificationService $notificationService)
	{
		$this->persistenceService = $persistenceService;
		$this->validationService = $validationService;
		$this->notificationService = $notificationService;
	}

	/**
	 * @static
	 * @param $reservationAction string|ReservationAction
	 * @param $persistenceService null|IReservationPersistenceService
	 * @param UserSession $session
	 * @return IReservationHandler
	 */
	public static function create($reservationAction, $persistenceService, JUser $user)
	{
		if (!isset($persistenceService))
		{
			$persistenceFactory = new RFFactoryReservationPersistence();
			$persistenceService = $persistenceFactory->create($reservationAction);
		}
		
		$validationFactory = new RFFactoryReservationValidation();
		$validationService = $validationFactory->create($reservationAction, $user);

		$notificationFactory = new RFFactoryReservationNotification();
		$notificationService = $notificationFactory->create($reservationAction, $user);

		return new RFReservationHandler($persistenceService, $validationService, $notificationService);
	}

	/**
	 * @param RFReservationSeries $reservationSeries
	 * @param IReservationSaveResultsView $view
	 * @return bool if the reservation was handled or not
	 */
	public function handle(RFReservationSeries $reservationSeries, IReservationSaveResultView $view)
	{
		$validationResult = $this->validationService->validate($reservationSeries);
		$result = $validationResult->canBeSaved();
		
		if ($validationResult->canBeSaved())
		{
			try
			{
				$this->persistenceService->persist($reservationSeries);
			}
			catch (Exception $ex)
			{
				JLog::add('Error saving reservation: %s', $ex);
				throw($ex);
			}

			//$this->notificationService->notify($reservationSeries);

			$view->setSaveSuccessfulMessage($result);
		}
		else
		{
			$view->setSaveSuccessfulMessage($result);
			$view->setErrors($validationResult->getErrors());
		}

		$view->setWarnings($validationResult->getWarnings());

		return $result;
	}
}