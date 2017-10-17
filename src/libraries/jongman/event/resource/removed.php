<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFEventResourceRemoved extends RFEventSeries
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
	public function __construct(RFResourceBookable $resource, RFReservationExistingSeries $series)
	{
		$this->resource = $resource;

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

}
