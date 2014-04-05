<?php
defined('_JEXEC') or die;

class RFEventResourceAdded extends RFSeriesEvent
{
	/**
	 * @var RFResourceBookable
	 */
	private $resource;

	/**
	 * @var int|ResourceLevel
	 */
	private $resourceLevel;

	/**
	 * @param RFResourceBookable $resource
	 * @param int|ResourceLevel $resourceLevel
	 * @param RFReservationExistingseries $series
	 */
	public function __construct(RFResourceBookable $resource, $resourceLevel, RFReservationExistingseries $series)
	{
		$this->resource = $resource;
		$this->resourceLevel = $resourceLevel;

		parent::__construct($series, RFEventPriority::Low);
	}

	/**
	 * @return RFResourceBookable
	 */
	public function resource()
	{
		return $this->resource;
	}

	public function resourceId()
	{
		return $this->resource->getResourceId();
	}

	/**
	 * @return RFReservationExistingseries
	 */
	public function series()
	{
		return $this->series;
	}

	public function __toString()
	{
		return sprintf("%s%s%s", get_class($this), $this->resourceId(), $this->series->seriesId());
	}

	public function resourceLevel()
	{
		return $this->resourceLevel;
	}
}