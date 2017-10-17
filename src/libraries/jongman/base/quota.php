<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IQuota
{
	/**
	 * @param ReservationSeries $reservationSeries
	 * @param User $user
	 * @param Schedule $schedule
	 * @param IReservationViewRepository $reservationViewRepository
	 * @return bool
	 */
	public function exceedsQuota($reservationSeries, $user, $schedule, IReservationViewRepository $reservationViewRepository);
}


interface IQuotaDuration
{
	/**
	 * @abstract
	 * @return string QuotaDuration
	 */
	public function name();

	/**
	 * @param ReservationSeries $reservationSeries
	 * @param string $timezone
	 * @return QuotaSearchDates
	*/
	public function getSearchDates(RFReservationSeries $reservationSeries, $timezone);

	/**
	 * @abstract
	 * @param DateRange $dateRange
	 * @return array|DateRange[]
	*/
	public function split(RFDateRange $dateRange);

	/**
	 * @abstract
	 * @param Date $date
	 * @return string
	*/
	public function getDurationKey(RFDate $date);
}

interface IQuotaLimit
{
	/**
	 * @abstract
	 * @param Date $start
	 * @param Date $end
	 * @param string $key
	 * @return void
	 * @throws QuotaExceededException
	 */
	public function tryAdd($start, $end, $key);

	/**
	 * @abstract
	 * @return decimal
	*/
	public function amount();

	/**
	 * @abstract
	 * @return string|QuotaUnit
	*/
	public function name();
}