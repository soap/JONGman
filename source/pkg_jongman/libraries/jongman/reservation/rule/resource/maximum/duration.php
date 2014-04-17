<?php
defined('_JEXEC') or die;

class RFReservationRuleResourceMaximumDuration implements IReservationValidationRule
{
	private $message;
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
			JLog::add("Resource : {$resource->getId()} has maximum duration ? {$resource->hasMaxLength()}", JLog::DEBUG, 'validation');
			if ($resource->hasMaxLength())
			{
				JLog::add("   Maximum duration is {$resource->getMaxLength()->interval()}", JLog::DEBUG, 'validation');
				$maxDuration = $resource->getMaxLength()->interval();
				$start = $reservationSeries->currentInstance()->startDate();
				$end = $reservationSeries->currentInstance()->endDate();
				
				$maxEnd = $start->applyDifference($maxDuration);
				if ($end->greaterThan($maxEnd))
				{
					$this->message = JText::sprintf("COM_JONGMAN_ERROR_RULE_MAX_DURATION", $maxDuration);
					return new RFReservationRuleResult(false, $this->message );
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