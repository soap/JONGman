<?php
defined('_JEXEC') or die;

class RFEventSeriesDeleted extends RFSeriesEvent
{
	public function __construct(ExistingReservationSeries $series)
	{
		parent::__construct($series, SeriesEventPriority::Highest);
	}

	/**
	 * @return ExistingReservationSeries
	 */
	public function Series()
	{
		return $this->series;
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->series->SeriesId());
	}
}