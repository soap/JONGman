<?php
defined('_JEXEC') or die;

abstract class RFQuotaDuration
{
	const Day = 'day';
	const Week = 'week';
	const Month = 'month';

	/**
	 * @param ReservationSeries $reservationSeries
	 * @return array|Date[]
	 */
	protected function GetFirstAndLastReservationDates(ReservationSeries $reservationSeries)
	{
		/** @var $instances Reservation[] */
		$instances = $reservationSeries->Instances();
		usort($instances, array('Reservation', 'Compare'));

		return array($instances[0]->StartDate(), $instances[count($instances) - 1]->EndDate());
	}
}