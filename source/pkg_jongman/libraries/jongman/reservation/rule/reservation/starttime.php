<?php
defined('_JEXEC') or die;

class RFReservationRuleReservationStarttime implements IReservationValidationRule
{
	protected $message;
	
	public function __construct(/*IScheduleRepository $scheduleRepository*/)
	{
		//$this->scheduleRepository = $scheduleRepository;
	}

	public function getError()
	{
		return $this->message;
	}
	/**
	 * @param ReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	public function validate($reservationSeries)
	{
		$constraint = JComponentHelper::getParams('com_jongman')->get('startTimeConstraint');

		if (empty($constraint))
		{
			$constraint = RFReservationStarttimeConstraint::_DEFAULT;
		}

		if ($constraint == RFReservationStarttimeConstraint::NONE)
		{
			// Ok
			return new RFReservationValidationResult();
		}

		$currentInstance = $reservationSeries->currentInstance();

		$dateThatShouldBeLessThanNow = $currentInstance->startDate();
		if ($constraint == RFReservationStarttimeConstraint::CURRENT)
		{
			$timezone = $dateThatShouldBeLessThanNow->timezone();
			$scheduleModel = JModelLegacy::getInstance('Schedule', 'JongmanSchedule');
			/** @var $currentPeriod SchedulePeriod */
			$currentPeriod = $scheduleModel->getScheduleLayout($reservationSeries->scheduleId(), $timezone)
				->getPeriod($currentInstance->endDate);
			/*
			$currentPeriod = $this->scheduleRepository
				->getLayout($reservationSeries->scheduleId(), new ScheduleLayoutFactory($timezone))
				->getPeriod($currentInstance->endDate());
			*/
			$dateThatShouldBeLessThanNow = $currentPeriod->beginDate();
		}
		JLog::add("Start Time Rule: Comparing {$dateThatShouldBeLessThanNow} to Date::Now()", JLog::DEBUG, 'validation');

		$startIsInFuture = $dateThatShouldBeLessThanNow->compare(RFDate::now()) >= 0;
		if (!$startIsInFuture) {
			$this->message = 'Start time is in the past';
			return new RFReservationRuleResult(false, $this->getError());
		}
		
		return new RFReservationRuleResult();
	}
	
}