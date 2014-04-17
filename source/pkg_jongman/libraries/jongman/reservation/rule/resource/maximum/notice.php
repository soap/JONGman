<?php
defined('_JEXEC') or die;

class RFReservationRuleResourceMaximumNotice implements IReservationValidationRule
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
			JLog::add("Resource : {$resource->getId()} has maximum notice time ? {$resource->hasMaxNotice()}", JLog::DEBUG, 'validation');
			if ($resource->hasMaxNotice())
			{
				JLog::add("   Maximum notice is {$resource->getMaxNotice()->interval()}", JLog::DEBUG, 'validation');
				$maxStartDate = RFDate::now()->applyDifference($resource->getMaxNotice()->interval());
				/* @var $instance RFReservation */
				foreach ($reservationSeries->getInstances() as $instance)
				{
					if ($instance->startDate()->greaterThan($maxStartDate))
					{
						$this->message = JText::sprintf("COM_JONGMAN_ERROR_RULE_MAX_NOTICE",$maxStartDate->format("Y-m-d H:i:s"));
						return new RFReservationRuleResult(false, $this->message);
					}
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