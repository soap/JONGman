<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRuleReservationStarttime implements IReservationValidationRule
{
	protected $__message;
	protected $__scheudleRepository;
	
	public function __construct(/*IScheduleRepository $scheduleRepository*/)
	{
		$scheduleRepository = new RFScheduleRepository();
		$this->__scheduleRepository = $scheduleRepository;
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
			
			//$scheduleModel = JModelLegacy::getInstance('Schedule', 'JongmanSchedule');
			/** @var $currentPeriod SchedulePeriod */
			//$currentPeriod = $scheduleModel->getScheduleLayout($reservationSeries->scheduleId(), $timezone)
			//	->getPeriod($currentInstance->endDate);
			
			$currentPeriod = $this->__scheduleRepository
				->getLayout($reservationSeries->scheduleId(), RFFactory::getScheduleLayout($timezone))
				->getPeriod($currentInstance->endDate());
			
			$dateThatShouldBeLessThanNow = $currentPeriod->beginDate();
		}
		JLog::add("Start Time Rule: Comparing {$dateThatShouldBeLessThanNow} to Date::Now()", JLog::DEBUG, 'validation');

		$startIsInFuture = $dateThatShouldBeLessThanNow->compare(RFDate::now()) >= 0;
		if (!$startIsInFuture) {
			$this->__message = JText::_('COM_JONGMAN_ERROR_RULE_STARTTIME_IN_THE_PAST');
			return new RFReservationRuleResult(false, $this->getError());
		}
		
		return new RFReservationRuleResult(true);
	}
	
	public function getError()
	{
		return $this->__message;
	}	
}