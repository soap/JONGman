<?php
defined('_JEXEC') or die;
/**
 * 
 * Allow user to bypass base rule if user is admin
 * @author Prasit Gebsaap
 *
 */
class RFReservationRuleAdminexcluded implements IReservationValidationRule
{
	/*
	 * base rule to be excluded if user is administrator
	 */
	private $rule;

	private $user;
	/**
	 * @param IReservationValidationRule $baseRule
	 * @param JUser $user
	 */
	public function __construct(IReservationValidationRule $baseRule, JUser $user)
	{
		$this->rule = $baseRule;
		$this->user = $user;
	}

	public function validate($reservationSeries)
	{
		JLog::add("Checking admin exclude exception for rule ".get_class($this->rule), JLog::DEBUG, 'validation');
		if ($this->user->authorise('core.admin', 'com_jongman'))
		{
			JLog::add("  Having admin right", JLog::DEBUG, 'validation');
			return new RFReservationRuleResult(true);
		}
		JLog::add("  No admin right", JLog::DEBUG, 'validation');
		return $this->rule->validate($reservationSeries);
	}
	
	public function getError()
	{
		return '';
	}
}