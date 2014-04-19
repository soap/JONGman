<?php
defined('_JEXEC') or die;

class RFQuotaDurationDay extends RFQuotaDuration implements IQuotaDuration
{
	/**
	 * @param RFReservationSeries $reservationSeries
	 * @param string $timezone
	 * @return RFQuotaSearchDates
	 */
	public function getSearchDates(RFReservationSeries $reservationSeries, $timezone)
	{
		$dates = $this->getFirstAndLastReservationDates($reservationSeries);

		$startDate = $dates[0]->toTimezone($timezone)->getDate();
		$endDate = $dates[1]->toTimezone($timezone)->addDays(1)->getDate();

		return new RFQuotaSearchDates($startDate, $endDate);
	}

	public function split(RFDateRange $dateRange)
	{
		$start = $dateRange->GetBegin();
		$end = $dateRange->GetEnd();

		$ranges = array();

		if (!$start->dateEquals($end))
		{
			$beginningOfNextDay = $start->addDays(1)->getDate();
			$ranges[] = new RFDateRange($start, $beginningOfNextDay);

			$currentDate = $beginningOfNextDay;

			for ($i = 1; $currentDate->lessThan($end) < 0; $i++)
			{
				$currentDate = $start->addDays($i);
				$ranges[] = new RFDateRange($currentDate, $currentDate->addDays(1)->getDate());
			}

			$ranges[] = new RFDateRange($currentDate, $end);
		}
		else
		{
			$ranges[] = new RFDateRange($start, $end);
		}

		return $ranges;
	}

	/**
	 * @param RFDate $date
	 * @return string
	 */
	public function getDurationKey(RFDate $date)
	{
		return sprintf("%s%s%s", $date->year(), $date->month(), $date->day());
	}

	/**
	 * @return string RFQuotaDuration
	 */
	public function name()
	{
		return RFQuotaDuration::Day;
	}
}