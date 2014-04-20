<?php
defined('_JEXEC') or die;

abstract class RFQuotaDuration
{
	const Day = 'day';
	const Week = 'week';
	const Month = 'month';

	/**
	 * @param RFReservationSeries $reservationSeries
	 * @return array|RFDate[]
	 */
	protected function getFirstAndLastReservationDates(RFReservationSeries $reservationSeries)
	{
		/** @var $instances Reservation[] */
		$instances = $reservationSeries->getInstances();
		usort($instances, array('RFReservation', 'compare'));

		return array($instances[0]->startDate(), $instances[count($instances) - 1]->endDate());
	}
}