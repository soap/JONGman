<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRuleReservationDatetime implements IReservationValidationRule
{
	private $__message = '';
	
	public function validate($reservationSeries)
	{
		$instance = $reservationSeries->currentInstance();
		$startBeforeEnd = $instance->startDate()->lessThan($instance->endDate());
		if (!$startBeforeEnd) {
			$this->__message = JText::_('COM_JONGMAN_ERROR_STARTDATE_LESSTHAN_ENDDATE');
			return new RFReservationReuleResult(false, $this->__message);
		}
		return new RFReservationRuleResult();
	}
	
	public function getError()
	{
		return $this->__message;
	}
}