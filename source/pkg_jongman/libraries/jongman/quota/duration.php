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
	protected function getFirstAndLastReservationDates(RFReservationSeries $reservationSeries)
	{
		/** @var $instances Reservation[] */
		$instances = $reservationSeries->getInstances();
		usort($instances, array('Reservation', 'compare'));

		return array($instances[0]->startDate(), $instances[count($instances) - 1]->endDate());
	}
}