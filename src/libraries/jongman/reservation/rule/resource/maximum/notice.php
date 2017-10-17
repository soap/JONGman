<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRuleResourceMaximumNotice implements IReservationValidationRule
{
	
	protected $__message;
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
			if ($resource->hasMaxNotice())
			{
				$maxStartDate = RFDate::now()->applyDifference($resource->getMaxNotice()->interval());
		
				/* @var $instance RFReservation */
				foreach ($reservationSeries->getInstances() as $instance)
				{
					if ($instance->startDate()->greaterThan($maxStartDate))
					{
						$this->__message = JText::sprintf("COM_JONGMAN_ERROR_RULE_MAX_NOTICE",$maxStartDate->format("Y-m-d H:i:s") );
						return new RFReservationRuleResult(false, $this->__message);
					}
				}
			}
		}
		
		return new RFReservationRuleResult();
	}

	public function getError()
	{
		$result = '';
		return $result;
	}	
}