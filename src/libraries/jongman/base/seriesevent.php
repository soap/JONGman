<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


abstract class RFSeriesEvent
{
	/**
	 * @var int
	 */
	private $priority;

	/**
	 * @var \ReservationSeries
	 */
	protected $series;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @param int|SeriesEventPriority $priority
	 * @return void
	 */
	protected function setPriority($priority)
	{
		$this->priority = $priority;
	}

	/**
	 * @return int|SeriesEventPriority
	 */
	public function getPriority()
	{
		return $this->priority;
	}

	/**
	 * @return ReservationSeries
	 */
	public function series()
	{
		return $this->series;
	}

	/**
	 * @return int
	 */
	public function seriesId()
	{
		return $this->series->seriesId();
	}

	/**
	 * @param ReservationSeries $series
	 * @param int|SeriesEventPriority $priority
	 */
	public function __construct(RFReservationSeries $series, $priority = RFEventPriority::Normal)
	{
		$this->priority = $priority;
		$this->series = $series;
		$this->id = $this->series->seriesId();
	}

	public function __toString()
	{
		return sprintf("%s-%s", get_class($this), $this->id);
	}

	public static function compare(RFSeriesEvent $event1, RFSeriesEvent $event2)
	{
		if ($event1->getPriority() == $event2->getPriority())
		{
			return 0;
		}

		// higher priority should be at the top
		return ($event1->getPriority() > $event2->getPriority()) ? -1 : 1;
	}
}