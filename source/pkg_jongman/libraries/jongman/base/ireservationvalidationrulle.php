<?php
defined('_JEXEC') or die;

interface IReservationValidationRule
{
	/**
	 * @param RFReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	public function validate($reservationSeries);
	
	/**
	 * 
	 * Get error message if validate failed, return empty string if not
	 */
	public function getMessage();
	
}