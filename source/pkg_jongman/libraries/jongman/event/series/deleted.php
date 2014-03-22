<?php
defined('_JEXEC') or die;

class RFEventSeriesDeleted extends RFSeriesEvent
{
	public function __construct(RFReservationExistingseries $series)
	{
		parent::__construct($series, RFEventPriority::Highest);
	}

	/**
	 * @return ExistingReservationSeries
	 */
	public function series()
	{
		return $this->series;
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->series->seriesId());
	}
}