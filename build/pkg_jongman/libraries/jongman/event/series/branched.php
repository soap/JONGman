<?php
defined('_JEXEC') or die;

class RFEventSeriesBranched extends RFSeriesEvent
{
	public function __construct(RFReservationSeries $series)
	{
		parent::__construct($series);
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->series->seriesId());
	}
}