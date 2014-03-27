<?php
defined('_JEXEC') or die;

class RFValidationRuleResourceAvailable implements IReservationValidationRule
{
	protected $message;
	
	public function validate($reservationSeries)
	{
		return true;
	}

	public function getError()
	{
		return $this->message;
	}
}