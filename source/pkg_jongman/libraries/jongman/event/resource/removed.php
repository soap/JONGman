<?php
defined('_JEXEC') or die;


class ResourceAddedEvent extends SeriesEvent
{
	/**
	 * @var BookableResource
	 */
	private $resource;

	/**
	 * @var int|ResourceLevel
	 */
	private $resourceLevel;

	/**
	 * @param BookableResource $resource
	 * @param int|ResourceLevel $resourceLevel
	 * @param ExistingReservationSeries $series
	 */
	public function __construct(RFResourceBookable $resource, $resourceLevel, RFReservationExistingseries $series)
	{
		$this->resource = $resource;
		$this->resourceLevel = $resourceLevel;

		parent::__construct($series, RFEventPriority::Low);
	}

	/**
	 * @return BookableResource
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
	 * @return ExistingReservationSeries
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
