<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRuleResourceMinimumNotice implements IReservationValidationRule
{
	protected $__message = '';
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
			if ($resource->hasMinNotice())
			{
				$minStartDate = RFDate::now()->applyDifference($resource->getMinNotice()->interval());
		
				/* @var $instance RFReservation */
				foreach ($reservationSeries->getInstances() as $instance)
				{
					// reservation cannot be less than min notice time
					if ($instance->startDate()->lessThan($minStartDate))
					{
						$this->__message = JText::sprintf("COM_JONGMAN_ERROR_RULE_MIN_NOTICE", $minStartDate->format("Y-m-d H:i:s"));
						return new RFReservationRuleResult(false, $this->__message);
					}
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