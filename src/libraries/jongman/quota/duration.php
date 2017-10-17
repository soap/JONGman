<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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
		usort($instances, array('RFReservation', 'compare'));

		return array($instances[0]->startDate(), $instances[count($instances) - 1]->endDate());
	}
}