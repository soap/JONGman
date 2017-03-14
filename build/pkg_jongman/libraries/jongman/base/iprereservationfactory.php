<?php
defined('_JEXEC') or die;

interface IPreReservationFactory
{
	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	public function createPreAddService(JUser $user);

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	*/
	public function createPreUpdateService(JUser $user);

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	*/
	public function createPreDeleteService(JUser $user);
}


