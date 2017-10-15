<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFEventOwnerChanged extends RFSeriesEvent
{
	/**
	 * @var int
	 */
	private $oldOwnerId;

	/**
	 * @var int
	 */
	private $newOwnerId;

	/**
	 * @param ExistingReservationSeries $series
	 * @param int $oldOwnerId
	 * @param int $newOwnerId
	 */
	public function __construct(RFReservationExistingSeries $series, $oldOwnerId, $newOwnerId)
	{
		$this->series = $series;
		$this->oldOwnerId = $oldOwnerId;
		$this->newOwnerId = $newOwnerId;
	}

	/**
	 * @return ExistingReservationSeries
	 */
	public function series()
	{
		return $this->series;
	}

	/**
	 * @return int
	 */
	public function oldOwnerId()
	{
		return $this->oldOwnerId;
	}

	/**
	 * @return int
	 */
	public function newOwnerId()
	{
		return $this->newOwnerId;
	}

	public function __toString()
	{
		return sprintf("%s%s%s%s", get_class($this), $this->oldOwnerId(), $this->newOwnerId(),
				$this->series->seriesId());
	}
}