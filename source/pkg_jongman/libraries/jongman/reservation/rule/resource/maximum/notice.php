<?php
defined('_JEXEC') or die;

class RFReservationRuleResourceMaximumNotice implements IReservationValidationRule
{
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
				$maxStartDate = Date::Now()->applyDifference($resource->getMaxNotice()->interval());
		
				/* @var $instance RFReservation */
				foreach ($reservationSeries->getInstances() as $instance)
				{
					if ($instance->startDate()->greaterThan($maxStartDate))
					{
						return new RFReservationRuleResult(false, 
							JText::sprintf("COM_JONGMAN_ERROR_MAX_NOTICE",$maxStartDate->format("Y-m-d H:i:s") ));
					}
				}
			}
		}
		
		return new RFReservationRuleResult();
	}
}