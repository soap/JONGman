<?php
defined('_JEXEC') or die;

class AdminSlotLabelFactory extends SlotLabelFactory
{
	protected function getFullName(ReservationItemView $reservation)
	{
		$name = new FullName($reservation->FirstName, $reservation->LastName);
		return $name->__toString();
	}
}