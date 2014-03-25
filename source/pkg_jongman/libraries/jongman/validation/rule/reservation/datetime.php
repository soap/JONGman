<?php
defined('_JEXEC') or die;

class RFValidationRuleReservationDatetime implements IReservationValidationRule
{
	private $message = '';
	
	public function validate($reservationSeries)
	{
		$instance = $reservationSeries->currentInstance();
		$startBeforeEnd = $instance->startDate()->lessThan($instance->endDate());
		if (!$startBeforeEnd) {
			$this->message = JText::_('COM_JONGMAN_ERROR_STARTDATE_LESSTHAN_ENDDATE');
		}
		return $startBeforeEnd;
	}
	
	public function getError()
	{
		return $this->message();
	}
}