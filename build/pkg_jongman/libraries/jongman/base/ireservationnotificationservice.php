<?php
defined('_JEXEC') or die;

interface IReservationNotificationService
{
	/**
	 * @param $reservationSeries RFReservationSeries|ExistingReservationSeries
	 * @return void
	 */
	function notify($reservationSeries);
}