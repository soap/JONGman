<?php
defined('_JEXEC') or die;

class SeriesBranchedEvent extends RFSeriesEvent
{
	public function __construct(ReservationSeries $series)
	{
		parent::__construct($series);
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->series->SeriesId());
	}
}