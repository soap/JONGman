<?php
defined('_JEXEC') or die;
/**
 * 
 * Update all instances in the series
 * @author Prasit Gebsaap
 *
 */
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
		/** @var JUser */
		$bookedBy = $series->bookedBy(); 
		if (!is_null($bookedBy) && $bookedBy->authorise('core.admin', 'com_jongman'))
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
		$startTimeConstraint = 'none';

		if (RFReservationStarttimeConstraint::isCurrent($startTimeConstraint))
		{
			return $series->currentInstance()->startDate();
		}

		if (RFReservationStarttimeConstraint::isNone($startTimeConstraint))
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
		$this->hasSameConfiguration = $targetRepeatOptions->hasSameConfigurationAs($series->getRepeatOptions());

		return parent::canChangeRepeatTo($series, $targetRepeatOptions);
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
		var_dump($this->hasSameConfiguration); jexit();
		if ($this->hasSameConfiguration)
		{
			$newEndDate = $series->repeatOptions()->terminationDate();
			// remove all instances past the new end date
			return $instance->startDate()->greaterThan($newEndDate);
		}
		var_dump($this->startDate());
		var_dump($this->earliestDateToKeep($series)); 
		jexit();
		// remove all current instances, which now have an incompatible configuration
		return $instance->startDate()->greaterThan($this->earliestDateToKeep($series));
	}	
} 