<?php
defined('_JEXEC') or die;

class RFReservationRuleResourceMinimumDuration implements IReservationValidationRule
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
			if ($resource->hasMinLength())
			{
				$minDuration = $resource->getMinLength()->interval();
				$start = $reservationSeries->currentInstance()->startDate();
				$end = $reservationSeries->currentInstance()->endDate();
				
				$minEnd = $start->applyDifference($minDuration);
				if ($end->greaterThan($minEnd))
				{
					return new RFReservationRuleResult(false, JText::sprintf("COM_JONGMAN_ERROR_MIN_DURATION", $minDuration));
				}
			}
		}
		
		return new RFReservationRuleResult();
	}
}