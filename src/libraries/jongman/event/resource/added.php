<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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
	 * @param RFReservationExistingSeries $series
	 */
	public function __construct(RFResourceBookable $resource, $resourceLevel, RFReservationExistingSeries $series)
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
	 * @return RFReservationExistingSeries
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