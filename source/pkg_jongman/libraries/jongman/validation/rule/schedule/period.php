<?php
defined('_JEXEC') or die;

/**
 * 
 * Validate if reservation is in reservable schedule time slots
 * @author Prasit Gebsaap
 * @todo incomplete
 */
class RFValidationRuleSchedulePeriod implements IReservationValidationRule
{
	private $repository;
	private $user;
	
	public function __construct(JModel $repository, JUser $user)
	{
		$this->repostiory = $repository;
		$this->user = $user;
	}
	
	/**
	 * @param RFReservationSeries $reservationSeries
	 * @return RFReservationValidationruleResult
	 */
	public function validate($reservationSeries)
	{
		$layout = $this->repostiory->getScheduleLayout(
			$reservationSeries->getResource()->getScheduleId(), 
			JongmanHelper::getUserTimezone()
		);

		$startDate = $reservationSeries->currentInstance()->startDate();
		$startPeriod = $layout->getPeriod($startDate);
		$endDate = $reservationSeries->currentInstance()->endDate();
		$endPeriod = $layout->getPeriod($endDate);

		$errors = array();
		if ($startPeriod == null || !$startPeriod->isReservable() || !$startPeriod->beginDate()->equals($startDate))
		{
			$errors[] = JText::_("COM_JONGMAN_ERROR_INVALID_START_TIMESLOT");
		}

		if ($endPeriod == null || !$endPeriod->beginDate()->equals($endDate))
		{
			$errors[] = JText::_("COM_JONGMAN_ERROR_INVALID_END_TIMESLOT");
		}

		$this->message = $errors;

		return new RFReservationValidationResult(count($errors) == 0, $this->getError());
	}

	public function getError()
	{
		return $this->message;	
	}
}