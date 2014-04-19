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
			JLog::add("validate quota {$quota}", JLog::DEBUG, 'validation');
			// RFQuota
			if ($quota->exceedsQuota($reservationSeries, $user, $schedule, $this->reservationRepository))
			{
				JLog::add("  exceed quota", JLog::DEBUG, 'validation');
				$this->message = JText::_('COM_JONGMAN_ERROR_RULE_QUOTA_EXCEEDED');
				return new RFReservationRuleResult(false, JText::_('COM_JONGMAN_ERROR_RULE_QUOTA_EXCEEDED'));
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
