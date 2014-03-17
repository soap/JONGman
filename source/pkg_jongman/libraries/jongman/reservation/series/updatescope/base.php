<?php
defined('_JEXEC') or die;

abstract class RFReservationSeriesUpdatescopeBase implements ISeriesUpdateScope
{
	/**
	 * @var ISeriesDistinction
	 */
	protected $series;

	protected function __construct(){}

	/**
	 * @param ExistingReservationSeries $series
	 * @param Date $compareDate
	 * @return array
	 */
	protected function allInstancesGreaterThan($series, $compareDate)
	{
		$instances = array();
		foreach ($series->_instances() as $instance)
		{
			if ($compareDate == null || $instance->startDate()->compare($compareDate) >= 0)
			{
				$instances[] = $instance;
			}
		}

		return $instances;
	}

	protected abstract function earliestDateToKeep($series);

	public function getRepeatOptions($series)
	{
		return $series->repeatOptions();
	}

	/**
	 * @param ReservationSeries $series
	 * @param IRepeatOptions $targetRepeatOptions
	 * @return bool
	 */
	public function canChangeRepeatTo($series, $targetRepeatOptions)
	{
		return !$targetRepeatOptions->equals($series->repeatOptions());
	}

	public function shouldInstanceBeRemoved($series, $instance)
	{
		return $instance->startDate()->greaterThan($this->earliestDateToKeep($series));
	}
}