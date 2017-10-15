<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRuleUserIsOwner implements IReservationValidationRule
{
	/**
	 * @var UserSession
	 */
	private $user;

	public function __construct(JUser $user)
	{
		$this->user = $user;
	}

	/**
	 * @param RFReservationSeries $reservationSeries
	 * @return RFReservationRuleResult
	 */
	public function validate($reservationSeries)
	{
		return new RFReservationRuleResult($this->user->id == $reservationSeries->userId(), JText::_('COM_JONGMAN_ACCESS_IS_NOT_PERMITTED'));
	}
	
	public function getError()
	{
		return JText::_('COM_JONGMAN_WARING_ACCESS_RESTRICTED');
	}
}