<?php
defined('_JEXEC') or die;

class RFReservationRuleResourceMaximumDuration implements IReservationValidationRule
{
	private $message;
	/**
	 * @see IReservationValidationRule::validate()
	 * 
	 * @param ReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	public function validate($reservationSeries)
	{
		$resources = $reservationSeries->allResources();
		
		foreach ($resources as $resource)
		{
			if ($resource->hasMaxLength())
			{
				$maxDuration = $resource->getMaxLength()->interval();
				$start = $reservationSeries->currentInstance()->startDate();
				$end = $reservationSeries->currentInstance()->endDate();
				
				$maxEnd = $start->applyDifference($maxDuration);
				if ($end->greaterThan($maxEnd))
				{
					$this->message =  JText::sprintf("COM_JONGMAN_ERROR_MAX_DURATION", $maxDuration);
					return new RFReservationRuleResult(false, $this->message);
				}
			}
		}
		
		return new RFReservationRuleResult();
	}
	
	public function getError()
	{
		return $this->message;
	}
}