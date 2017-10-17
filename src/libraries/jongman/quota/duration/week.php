<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFQuotaDurationWeek extends RFQuotaDuration implements IQuotaDuration
{
	/**
	 * @param RFReservationSeries $reservationSeries
	 * @param string $timezone
	 * @return QuotaSearchDates
	 */
	public function getSearchDates(RFReservationSeries $reservationSeries, $timezone)
	{
		$dates = $this->getFirstAndLastReservationDates($reservationSeries);

		$startDate = $dates[0]->ToTimezone($timezone);
		$daysFromWeekStart = $startDate->weekday();
		$startDate = $startDate->addDays(-$daysFromWeekStart)->getDate();

		$endDate = $dates[1]->toTimezone($timezone);
		$daysFromWeekEnd = 7 - $endDate->weekday();
		$endDate = $endDate->addDays($daysFromWeekEnd)->getDate();

		return new RFQuotaSearchDates($startDate, $endDate);
	}

	/**
	 * @param Date $date
	 * @return void
	 */
	public function getDurationKey(Date $date)
	{
		$daysFromWeekStart = $date->weekday();
		$firstDayOfWeek = $date->addDays(-$daysFromWeekStart)->getDate();
		return sprintf("%s%s%s", $firstDayOfWeek->Year(), $firstDayOfWeek->month(), $firstDayOfWeek->day());
	}

	/**
	 * @param DateRange $dateRange
	 * @return array|DateRange[]
	 */
	public function split(RFDateRange $dateRange)
	{
		$start = $dateRange->getBegin();
		$end = $dateRange->getEnd();

		$ranges = array();

		if (!$start->dateEquals($end))
		{
			$nextWeek = $this->getStartOfNextWeek($start);

			if ($nextWeek->lessThan($end))
			{
				$ranges[] = new DateRange($start, $nextWeek);
				while ($nextWeek->LessThan($end))
				{
					$thisEnd = $this->getStartOfNextWeek($nextWeek);

					if ($thisEnd->lessThan($end))
					{
						$ranges[] = new RFDateRange($nextWeek, $thisEnd);
					}
					else
					{
						$ranges[] = new RFDateRange($nextWeek, $end);
					}

					$nextWeek = $thisEnd;
				}
			}
			else
			{
				$ranges[] = new RFDateRange($start, $end);
			}
		}
		else
		{
			$ranges[] = new RFDateRange($start, $end);
		}


		return $ranges;
	}

	/**
	 * @param Date $date
	 * @return Date
	 */
	private function getStartOfNextWeek(Date $date)
	{
		$daysFromWeekEnd = 7 - $date->weekday();
		return $date->addDays($daysFromWeekEnd)->getDate();
	}

	/**
	 * @return string QuotaDuration
	 */
	public function name()
	{
		return RFQuotaDuration::Week;
	}
}