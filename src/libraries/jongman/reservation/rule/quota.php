<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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
	private $reservationViewRepository;

	/**
	 * @var \IUserRepository
	 */
	private $userRepository;

	/**
	 * @var \IScheduleRepository
	 */
	private $scheduleRepository;
	
	private $message;

	public function __construct(IQuotaRepository $quotaRepository, IReservationViewRepository $reservationViewRepository, IUserRepository $userRepository, IScheduleRepository $scheduleRepository)
	{
		$this->quotaRepository = $quotaRepository;
		$this->reservationViewRepository = $reservationViewRepository;
		$this->userRepository = $userRepository;
		$this->scheduleRepository = $scheduleRepository;
	}

	/**
	 * @param ReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	public function validate($reservationSeries)
	{
		$quotas = $this->quotaRepository->loadAll();
		$user = $this->userRepository->loadById($reservationSeries->userId());
		$schedule = $this->scheduleRepository->loadById($reservationSeries->scheduleId());
		
		foreach ($quotas as $quota)
		{
			if ($quota->exceedsQuota($reservationSeries, $user, $schedule, $this->reservationViewRepository))
			{
				$this->message = JText::_('COM_JONGMAN_RULE_QUOTA_EXCEEDED');
				return new RFReservationRuleResult(false, $this->message);
			}
		}

		return new RFReservationRuleResult();
	}

	public function getError()
	{
		return $this->message;	
	}
}
