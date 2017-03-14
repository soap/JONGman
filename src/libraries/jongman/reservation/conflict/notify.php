<?php
defined('_JEXEC') or die;

class RFReservationConflictNotify extends RFReservationConflictResolution
{
	/**
	 * @param ReservationItemView $existingReservation
	 * @return bool
	 */
	public function handle(RFReservationItemView $existingReservation)
	{
		return false;
	}
}