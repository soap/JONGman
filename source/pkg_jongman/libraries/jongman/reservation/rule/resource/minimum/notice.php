<?php
defined('_JEXEC') or die;

class RFReservationRuleResourceMinimumNotice implements IReservationValidationRule
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
			JLog::add("Resource : {$resource->getId()} has minimum notice time ? {$resource->hasMinNotice()}", JLog::DEBUG, 'validation');
			if ($resource->hasMinNotice())
			{	
				JLog::add("   Minimum notice time is {$resource->getMinNotice()->interval()}", JLog::DEBUG, 'validation');
				$minStartDate = RFDate::now()->applyDifference($resource->getMinNotice()->interval());
		
				/* @var $instance RFReservation */
				foreach ($reservationSeries->getInstances() as $instance)
				{
					if ($instance->startDate()->greaterThan($minStartDate))
					{
						$this->message = JText::sprintf("COM_JONGMAN_ERROR_MIN_NOTICE",$minStartDate->format("Y-m-d H:i:s") ); 
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