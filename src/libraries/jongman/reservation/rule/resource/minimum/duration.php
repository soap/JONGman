<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRuleResourceMinimumDuration implements IReservationValidationRule
{
	private $__message;
	/**
	 * @see IReservationValidationRule::validate()
	 * 
	 * @param RFReservationSeries $reservationSeries
	 * @return RFReservationRuleResult
	 */
	public function validate($reservationSeries)
	{
		$resources = $reservationSeries->allResources();
		
		foreach ($resources as $resource)
		{
			if ($resource->hasMinLength())
			{
				$minDuration = $resource->getMinLength()->interval();
				$start = $reservationSeries->currentInstance()->startDate();
				$end = $reservationSeries->currentInstance()->endDate();
				
				$minEnd = $start->applyDifference($minDuration);
				if ($end->lessThan($minEnd))
				{
					$this->__message =  JText::sprintf("COM_JONGMAN_ERROR_MIN_DURATION", $minDuration->minutes());
					return new RFReservationRuleResult(false, $this->__message);
				}
			}
		}
		
		return new RFReservationRuleResult();
	}
	
	public function getError()
	{
		return $this->__message;
	}
}