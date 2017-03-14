<?php
defined('_JEXEC') or die;

interface IReservationPersistenceFactory
{
	/**
	 * @param ReservationAction $reservationAction
	 * @return IReservationPersistenceService
	 */
	function create($reservationAction);
}
