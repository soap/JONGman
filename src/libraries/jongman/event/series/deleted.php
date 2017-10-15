<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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