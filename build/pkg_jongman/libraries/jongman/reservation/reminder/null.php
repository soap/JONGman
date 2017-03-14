<?php
defined('_JEXEC') or die;

class RFReservationReminderNull extends RFReservationReminder
{
	public function __construct()
	{
		parent::__construct(0, null);
	}

	public function enabled()
	{
		return false;
	}

	public function minutesPrior()
	{
		return 0;
	}
}