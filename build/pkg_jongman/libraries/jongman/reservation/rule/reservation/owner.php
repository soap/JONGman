<?php
defined('_JEXEC') or die;

class RFReservationRuleOwner implements IReservationValidationRule
{
	/**
	 * @var UserSession
	 */
	private $userSession;

	public function __construct(JUser $userSession)
	{
		$this->userSession = $userSession;
	}

	/**
	 * @param RFReservationSeries $reservationSeries
	 * @return RFReservationRuleResult
	 */
	public function validate($reservationSeries)
	{
		return new RFReservationRuleResult($this->userSession->id == $reservationSeries->userId(), JText::_('COM_JONGMAN_ACCESS_IS_NOT_PERMITTED'));
	}
}