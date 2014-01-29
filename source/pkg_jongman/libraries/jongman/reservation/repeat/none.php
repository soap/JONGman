<?php
defined('_JEXEC') or die;


class RFReservationRepeatNone implements IRepeatOptions
{
	public function getDates(RFDateRange $startingDate)
	{
		return array();
	}

	public function repeatType()
	{
		return RFReservationRepeatType::None;
	}

	public function configurationString()
	{
		return '';
	}

	public function equals(IRepeatOptions $repeatOptions)
	{
		return get_class($this) == get_class($repeatOptions);
	}

	public function hasSameConfigurationAs(IRepeatOptions $repeatOptions)
	{
		return $this->equals($repeatOptions);
	}

	public function terminationDate()
	{
		return RFDate::now();
	}
}