<?php
defined('_JEXEC') or die;


class RFReservationRepeatOptionsFactory
{
	/**
	 * @param string $repeatType must be option in RFReservationRepeatType enum
	 * @param int $interval
	 * @param RFDate $terminationDate
	 * @param array $weekdays
	 * @param string $monthlyType
	 * @return IRepeatOptions
	 */
	public static function create($repeatType, $interval, $terminationDate, $weekdays, $monthlyType)
	{
		switch ($repeatType)
		{
			case RFReservationRepeatType::DAILY :
				return new RFReservationRepeatDaily($interval, $terminationDate);

			case RFReservationRepeatType::WEEKLY :
				return new RFReservationRepeatWeekly($interval, $terminationDate, $weekdays);

			case RFReservationRepeatType::MONTHLY :
				return ($monthlyType == RFReservationRepeatMonthlytype::DayOfMonth) ? new RFReservationRepeatDayofmonth($interval, $terminationDate) : new RFReservationRepeatWeekDayOfMonth($interval, $terminationDate);

			case RFReservationRepeatType::YEARLY :
				return new RFReservationRepeatYearly($interval, $terminationDate);
		}

		return new RFReservationRepeatNone();
	}

	/**
	 * @param IRepeatOptionsComposite $composite
	 * @param string $terminationDateTimezone
	 * @return IRepeatOptions
	 */
	/*public function CreateFromComposite(IRepeatOptionsComposite $composite, $terminationDateTimezone)
	{
		$repeatType = $composite->GetRepeatType();
		$interval = $composite->GetRepeatInterval();
		$weekdays = $composite->GetRepeatWeekdays();
		$monthlyType = $composite->GetRepeatMonthlyType();
		$terminationDate = Date::Parse($composite->GetRepeatTerminationDate(), $terminationDateTimezone);

		return $this->Create($repeatType, $interval, $terminationDate, $weekdays, $monthlyType);
	}*/
}