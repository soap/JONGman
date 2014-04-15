<?php
defined('_JEXEC') or die;

jimport('jongman.utils.starttimeconstraint');

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
	 * @param RFReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	public function validate($reservationSeries)
	{
		$constraint = JComponentHelper::getParams('com_jongman')->get('startTimeConstraint');

		if (empty($constraint))
		{
			$constraint = RFReservationStartTimeConstraint::_DEFAULT;
		}

		if ($constraint == RFReservationStartTimeConstraint::NONE)
		{
			// Ok
			return new RFReservationRuleResult();
		}

		$currentInstance = $reservationSeries->currentInstance();

		$dateThatShouldBeLessThanNow = $currentInstance->startDate();
		if ($constraint == RFReservationStartTimeConstraint::CURRENT)
		{
			$timezone = $dateThatShouldBeLessThanNow->timezone();
			$scheduleModel = JModel::getInstance('Schedule', 'JongmanSchedule');
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
		JLog::add("Start Time Rule: Comparing {$dateThatShouldBeLessThanNow} to {RFDate::Now()}", JLog::DEBUG, 'validation');

		$startIsInFuture = $dateThatShouldBeLessThanNow->compare(RFDate::now()) >= 0;
		if (!$startIsInFuture) {
			$this->message = JText::_("COM_JONGMAN_ERROR_RULE_STARTTIME_IN_THE_PAST");
			return new RFReservationRuleResult(false, $this->getError());
		}
		
		return new RFReservationRuleResult();
	}
	
}