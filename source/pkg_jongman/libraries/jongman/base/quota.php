<?php
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
	public function exceedsQuota($reservationSeries, $user, $schedule, IReservationRepository $reservationRepository);
}


interface IQuotaDuration
{
	/**
	 * @abstract
	 * @return string RFQuotaDuration
	 */
	public function name();

	/**
	 * @param RFReservationSeries $reservationSeries
	 * @param string $timezone
	 * @return RFQuotaSearchDates
	*/
	public function getSearchDates(RFReservationSeries $reservationSeries, $timezone);

	/**
	 * Split for counting per duration
	 * @abstract
	 * @param RFDateRange $dateRange
	 * @return array|RFDateRange[]
	*/
	public function split(RFDateRange $dateRange);

	/**
	 * @abstract
	 * @param RFDate $date
	 * @return string
	*/
	public function getDurationKey(RFDate $date);
}

interface IQuotaLimit
{
	/**
	 * @abstract
	 * @param RFDate $start
	 * @param RFDate $end
	 * @param string $key
	 * @return void
	 * @throws RFQuotaExceededException
	 */
	public function tryAdd($start, $end, $key);

	/**
	 * @abstract
	 * @return decimal
	*/
	public function amount();

	/**
	 * @abstract
	 * @return string|RFQuotaUnit
	*/
	public function name();
}