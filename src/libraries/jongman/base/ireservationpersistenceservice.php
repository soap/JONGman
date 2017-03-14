<?php
defined('_JEXEC') or die;

interface IReservationPersistenceService
{
	/**
	 * @param RFReservationSeries $reservation
	 */
	function persist($reservation);
}
?>