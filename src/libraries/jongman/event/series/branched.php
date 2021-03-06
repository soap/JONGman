<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFEventSeriesBranched extends RFSeriesEvent
{
	public function __construct(RFReservationSeries $series)
	{
		parent::__construct($series);
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->series->seriesId());
	}
}