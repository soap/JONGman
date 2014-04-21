<?php
defined('_JEXEC') or die;

class RFReservationRuleQuota implements IReservationValidationRule
{
	/**
	 * @var \IQuotaRepository
	 */
	private $quotaRepository;

	/**
	 * @var \IReservationViewRepository
	 */
	private $reservationRepository;

	/**
	 * @var \IUserRepository
	 */
	private $userRepository;

	/**
	 * @var \IScheduleRepository
	 */
	private $scheduleRepository;

	public function __construct(IQuotaRepository $quotaRepository, IReservationRepository $reservationRepository, IUserRepository $userRepository, IScheduleRepository $scheduleRepository)
	{
		$this->quotaRepository = $quotaRepository;
		$this->reservationRepository = $reservationRepository;
		$this->userRepository = $userRepository;
		$this->scheduleRepository = $scheduleRepository;
	}

	/**
	 * @param RFReservationSeries $reservationSeries
	 * @return RFReservationRuleResult
	 */
	public function validate($reservationSeries)
	{
		$quotas = $this->quotaRepository->loadAll();
		$user = $this->userRepository->loadById($reservationSeries->userId());
		$schedule = $this->scheduleRepository->loadById($reservationSeries->scheduleId());
		
		foreach ($quotas as $quota)
		{	
			JLog::add("validate quota {$quota} {$quota->getLimit()->amount()} {$quota->getLimit()->name()}/{$quota->getDuration()->name()}", JLog::DEBUG, 'validation');
			// RFQuota
			if ($quota->exceedsQuota($reservationSeries, $user, $schedule, $this->reservationRepository))
			{
				if ($quota->getLimit()->name() == RFQuotaUnit::Reservations) {
					//Only {$this->totalAllowed} reservations are allowed for this duration"
					$this->message = JText::sprintf("COM_JONGMAN_ERROR_RULE_QUOTA_RESERVATIONS_EXCEED", $quota->getLimit()->amount(), $quota->getDuration()->name());		
				}else{
					$this->message = JText::sprintf("COM_JONGMAN_ERROR_RULE_QUOTA_HOURS_EXCEED", $quota->getLimit()->amount(), $quota->getDuration()->name());	
				}
				JLog::add(" exceeds quota, " .$this->message, JLog::DEBUG, 'validation');
				return new RFReservationRuleResult(false, $this->message);
			}
			
			JLog::add("  not exceed quota", JLog::DEBUG, 'validation');
		}

		return new RFReservationRuleResult();
	}
	
	public function getError()
	{
		return $this->message;
	}
}
