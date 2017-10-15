<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFQuotaDurationMonth extends RFQuotaDuration implements IQuotaDuration
{

	/**
	 * @param ReservationSeries $reservationSeries
	 * @param string $timezone
	 * @return QuotaSearchDates
	 */
	public function getSearchDates(RFReservationSeries $reservationSeries, $timezone)
	{
		$minMax = $this->getFirstAndLastReservationDates($reservationSeries);

		/** @var $start Date */
		$start = $minMax[0]->toTimezone($timezone);
		/** @var $end Date */
		$end = $minMax[1]->toTimezone($timezone);

		$searchStart = RFDate::create($start->year(), $start->month(), 1, 0, 0, 0, $timezone);
		$searchEnd = RFDate::create($end->year(), $end->month() + 1, 1, 0, 0, 0, $timezone);

		return new RFQuotaSearchDates($searchStart, $searchEnd);
	}

	/**
	 * @param DateRange $dateRange
	 * @return array|DateRange[]
	 */
	public function split(RFDateRange $dateRange)
	{
		$ranges = array();

		$start = $dateRange->getBegin();
		$end = $dateRange->getEnd();

		if (!$this->sameMonth($start, $end))
		{
			$current = $start;

			while (!$this->sameMonth($current, $end))
			{
				$next = $this->getFirstOfMonth($current, 1);

				$ranges[] = new RFDateRange($current, $next);

				$current = $next;

				if ($this->sameMonth($current, $end))
				{
					$ranges[] = new RFDateRange($current, $end);
				}
			}
		}
		else
		{
			$ranges[] = $dateRange;
		}

		return $ranges;
	}

	/**
	 * @param Date $date
	 * @param int $monthOffset
	 * @return Date
	 */
	private function getFirstOfMonth(RFDate $date, $monthOffset = 0)
	{
		return RFDate::create($date->year(), $date->month() + $monthOffset, 1, 0, 0, 0, $date->timezone());
	}

	/**
	 * @param Date $d1
	 * @param Date $d2
	 * @return bool
	 */
	private function sameMonth(RFDate $d1, RFDate $d2)
	{
		return ($d1->month() == $d2->month()) && ($d1->year() == $d2->year());
	}

	/**
	 * @param Date $date
	 * @return string
	 */
	public function getDurationKey(RFDate $date)
	{
		return sprintf("%s%s", $date->year(), $date->month());
	}

	/**
	 * @return string QuotaDuration
	 */
	public function name()
	{
		return RFQuotaDuration::Month;
	}
}