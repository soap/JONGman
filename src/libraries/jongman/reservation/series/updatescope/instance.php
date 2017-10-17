<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationSeriesUpdatescopeInstance extends RFReservationSeriesUpdatescopeBase
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getScope()
	{
		return RFReservationSeriesUpdatescope::THISINSTANCE;
	}

	public function getInstances($series)
	{
		return array($series->currentInstance());
	}

	public function requiresNewSeries()
	{
		return true;
	}

	public function earliestDateToKeep($series)
	{
		return $series->currentInstance()->startDate();
	}

	public function getRepeatOptions($series)
	{
		return new RFReservationRepeatNone();
	}

	public function canChangeRepeatTo($series, $targetRepeatOptions)
	{
		return $targetRepeatOptions->equals(new RFReservationRepeatNone());
	}

	public function shouldInstanceBeRemoved($series, $instance)
	{
		return false;
	}	
} 