<?php
defined('_JEXEC') or die;

class RFLayoutReservation extends RFLayoutSchedule implements ILayoutSchedule
{
	protected function spansMidnight(RFDate $start, RFDate $end)
	{
		return false;
	}
}