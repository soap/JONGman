<?php
defined('_JEXEC') or die;

class RFReservationValidationServiceDelete implements IReservationValidationService
{
	/**
	 * @var IReservationValidationService
	 */
	private $ruleProcessor;
	
	/**
	 * @param IReservationValidationService $ruleProcessor
	 */
	public function __construct($ruleProcessor)
	{
		$this->ruleProcessor = $ruleProcessor;
	}
	
	public function validate($reservationSeries)
	{
		return $this->ruleProcessor->validate($reservationSeries);
	}	
}