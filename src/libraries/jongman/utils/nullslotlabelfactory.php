<?php
defined('_JEXEC') or die;

class RFNullSlotLabelFactory extends RFSlotLabelFactory
{
	public function format(ReservationItemView $reservation)
	{
		return '';
	}
}
