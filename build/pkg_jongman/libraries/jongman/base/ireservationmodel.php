<?php

defined ('_JEXEC') or die;

interface IReservationModel
{
	/**
	 * @return ReservationSeries
	 */
	public function buildReservation();

	/**
	 * @param ReservationSeries $reservationSeries
	*/
	public function handleReservation($reservationSeries);
	
}