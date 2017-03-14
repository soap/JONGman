<?php
defined('_JEXEC') or die;
/**
 * 
 * Future Update scope logic 
 * @author Prasit Gebsaap
 *
 */
class RFReservationSeriesUpdatescopeFuture extends RFReservationSeriesUpdatescopeBase
{
	public function __construct()
	{
		parent::__construct();
	}

	public function getScope()
	{
		return RFReservationSeriesUpdatescope::FUTUREINSTANCES;
	}

	/**
	 * get instances that we have to update
	 * @see ISeriesUpdateScope::getInstances()
	 */
	public function getInstances($series)
	{
		return $this->allInstancesGreaterThan($series, $this->earliestDateToKeep($series));
	}

	public function earliestDateToKeep($series)
	{
		return $series->currentInstance()->startDate();
	}

	/**
	 * We always have to create new series for Future Scope
	 * @see ISeriesUpdateScope::requiresNewSeries()
	 */
	public function requiresNewSeries()
	{
		return true;
	}	
} 