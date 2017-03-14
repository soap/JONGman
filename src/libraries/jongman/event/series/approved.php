<?php
defined('_JEXEC') or die;

class RFEventSeriesApproved extends RFSeriesEvent
{
	public function __construct(RFReservationExistingSeries $series)
	{
		parent::__construct($series);
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->series->seriesId());
	}
}
