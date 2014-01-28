<?php
defined('_JEXEC') or die;


interface IReservationValidationRule
{
	/**
	 * @param RFReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	public function validate($reservationSeries);
}
?>