<?php
defined('_JEXEC') or die;

class RFEventSeriesDeleted extends RFSeriesEvent
{
	public function __construct(RFReservationExistingSeries $series)
	{
		parent::__construct($series, RFEventPriority::Highest);
	}

	/**
	 * @return RFExistingReservationSeries
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