<?php
defined('_JEXEC') or die;

interface IReservationValidationService
{
	/**
	 * @param ReservationSeries|ExistingReservationSeries $series
	 * @return IReservationValidationResult
	 */
	public function validate($series);
}