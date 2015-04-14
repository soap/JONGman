<?php
defined('_JEXEC') or die;

interface IReservationNotificationFactory
{
	/**
	 * @param RFReservationAction $reservationAction
	 * @param RFUserSession $userSession
	 * @return IReservationNotificationService
	 */
	function create($reservationAction, $userSession);
}