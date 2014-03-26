<?php
defined('_JEXEC') or die;


class RFQuotaDurationMonth extends RFQuotaDuration implements IQuotaDuration
{

	/**
	 * @param ReservationSeries $reservationSeries
	 * @param string $timezone
	 * @return QuotaSearchDates
	 */
	public function GetSearchDates(ReservationSeries $reservationSeries, $timezone)
	{
		$minMax = $this->GetFirstAndLastReservationDates($reservationSeries);

		/** @var $start Date */
		$start = $minMax[0]->ToTimezone($timezone);
		/** @var $end Date */
		$end = $minMax[1]->ToTimezone($timezone);

		$searchStart = Date::Create($start->Year(), $start->Month(), 1, 0, 0, 0, $timezone);
		$searchEnd = Date::Create($end->Year(), $end->Month() + 1, 1, 0, 0, 0, $timezone);

		return new QuotaSearchDates($searchStart, $searchEnd);
	}

	/**
	 * @param DateRange $dateRange
	 * @return array|DateRange[]
	 */
	public function Split(DateRange $dateRange)
	{
		$ranges = array();

		$start = $dateRange->GetBegin();
		$end = $dateRange->GetEnd();

		if (!$this->SameMonth($start, $end))
		{
			$current = $start;

			while (!$this->SameMonth($current, $end))
			{
				$next = $this->GetFirstOfMonth($current, 1);

				$ranges[] = new DateRange($current, $next);

				$current = $next;

				if ($this->SameMonth($current, $end))
				{
					$ranges[] = new DateRange($current, $end);
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
	private function GetFirstOfMonth(Date $date, $monthOffset = 0)
	{
		return Date::Create($date->Year(), $date->Month() + $monthOffset, 1, 0, 0, 0, $date->Timezone());
	}

	/**
	 * @param Date $d1
	 * @param Date $d2
	 * @return bool
	 */
	private function SameMonth(Date $d1, Date $d2)
	{
		return ($d1->Month() == $d2->Month()) && ($d1->Year() == $d2->Year());
	}

	/**
	 * @param Date $date
	 * @return string
	 */
	public function GetDurationKey(Date $date)
	{
		return sprintf("%s%s", $date->Year(), $date->Month());
	}

	/**
	 * @return string QuotaDuration
	 */
	public function Name()
	{
		return QuotaDuration::Month;
	}
}