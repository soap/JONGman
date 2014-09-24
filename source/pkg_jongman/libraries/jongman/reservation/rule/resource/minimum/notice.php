<?php
defined('_JEXEC') or die;

class RFReservationRuleResourceMinimumNotice implements IReservationValidationRule
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
			if ($resource->hasMinNotice())
			{
				$minStartDate = RFDate::Now()->applyDifference($resource->getMinNotice()->interval());
		
				/* @var $instance RFReservation */
				foreach ($reservationSeries->getInstances() as $instance)
				{
					if ($instance->startDate()->greaterThan($minStartDate))
					{
						return new RFReservationRuleResult(false, 
							JText::sprintf("COM_JONGMAN_ERROR_MIN_NOTICE", $minStartDate->format("Y-m-d H:i:s") ));
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