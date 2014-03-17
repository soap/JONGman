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
	public function __construct(BookableResource $resource, $resourceLevel, ExistingReservationSeries $series)
	{
		$this->resource = $resource;
		$this->resourceLevel = $resourceLevel;

		parent::__construct($series, SeriesEventPriority::Low);
	}

	/**
	 * @return BookableResource
	 */
	public function Resource()
	{
		return $this->resource;
	}

	public function ResourceId()
	{
		return $this->resource->GetResourceId();
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
		return sprintf("%s%s%s", get_class($this), $this->ResourceId(), $this->series->SeriesId());
	}

	public function ResourceLevel()
	{
		return $this->resourceLevel;
	}
}
