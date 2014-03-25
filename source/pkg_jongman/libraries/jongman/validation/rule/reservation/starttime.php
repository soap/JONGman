<?php
defined('_JEXEC') or die;

class RFValidationRuleReservationStarttme implements IReservationValidationRule
{
	public function __construct(IScheduleRepository $scheduleRepository)
	{
		$this->scheduleRepository = $scheduleRepository;
	}

	/**
	 * @param ReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	public function validate($reservationSeries)
	{
		$constraint = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_TIME_CONSTRAINT);

		if (empty($constraint))
		{
			$constraint = RFReservationStarttimeConstraint::_DEFAULT;
		}

		if ($constraint == RFReservationStarttimeConstraint::NONE)
		{
			return true;
		}

		$currentInstance = $reservationSeries->currentInstance();

		$dateThatShouldBeLessThanNow = $currentInstance->startDate();
		if ($constraint == RFReservationStarttimeConstraint::CURRENT)
		{
			$timezone = $dateThatShouldBeLessThanNow->timezone();
			/** @var $currentPeriod SchedulePeriod */
			$currentPeriod = $this->scheduleRepository
			->getLayout($reservationSeries->scheduleId(), new ScheduleLayoutFactory($timezone))
			->getPeriod($currentInstance->endDate());
			$dateThatShouldBeLessThanNow = $currentPeriod->beginDate();
		}
		Log::Debug("Start Time Rule: Comparing %s to %s", $dateThatShouldBeLessThanNow, Date::Now());

		$startIsInFuture = $dateThatShouldBeLessThanNow->compare(Date::now()) >= 0;
		$this->message = 'Start time is in the past';
		return false;
	}
}