<?php
defined('_JEXEC') or die;

class RFReservationSeriesUpdatescopeFull extends RFReservationSeriesUpdatescopeBase
{
	private $hasSameConfiguration = false;

	public function __construct()
	{
		parent::__construct();
	}

	public function getScope()
	{
		return RFReservationSeriesUpdatescope::FULLSERIES;
	}

	/**
	 * @param ExistingReservationSeries $series
	 * @return array
	 */
	public function getInstances($series)
	{
		$bookedBy = $series->bookedBy();
		if (!is_null($bookedBy) && $bookedBy->isAdmin)
		{
			return $series->_instances();
		}

		return $this->allInstancesGreaterThan($series, $this->earliestDateToKeep($series));
	}

	/**
	 * @param ExistingReservationSeries $series
	 * @return mixed
	 */
	public function earliestDateToKeep($series)
	{
		$startTimeConstraint = true;

		if (RRFeservationStartTimeConstraint::isCurrent($startTimeConstraint))
		{
			return $series->CurrentInstance()->startDate();
		}

		if (RFReservationStartTimeConstraint::isNone($startTimeConstraint))
		{
			return RFDate::min();
		}

		return RFDate::now();
	}

	/**
	 * @param ReservationSeries $series
	 * @param IRepeatOptions $targetRepeatOptions
	 * @return bool
	 */
	public function canChangeRepeatTo($series, $targetRepeatOptions)
	{
		$this->hasSameConfiguration = $targetRepeatOptions->hasSameConfigurationAs($series->repeatOptions());

		return parent::CanChangeRepeatTo($series, $targetRepeatOptions);
	}

	/**
	 * No need to create new series as we made FULL scope update
	 * @see ISeriesUpdateScope::requiresNewSeries()
	 */
	public function requiresNewSeries()
	{
		return false;
	}

	/**
	 * 
	 * @see ISeriesUpdateScope::shouldInstanceBeRemoved()
	 */
	public function shouldInstanceBeRemoved($series, $instance)
	{
		if ($this->hasSameConfiguration)
		{
			$newEndDate = $series->repeatOptions()->terminationDate();
			// remove all instances past the new end date
			return $instance->startDate()->greaterThan($newEndDate);
		}

		// remove all current instances, which now have an incompatible configuration
		return $instance->startDate()->greaterThan($this->earliestDateToKeep($series));
	}	
} 