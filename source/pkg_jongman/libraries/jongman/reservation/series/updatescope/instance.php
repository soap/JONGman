<?php
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

	public function instances($series)
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
		return new RepeatNone();
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