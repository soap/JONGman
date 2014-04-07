<?php
defined('_JEXEC') or die;
jimport('jongman.base.iresourceavailabilitystrategy');

class RFValidationRuleResourceAvailability implements IReservationValidationRule
{
	protected $message = array();
	
	protected $timezone;
	
	public function __construct(IResourceAvailabilityStrategy $strategy, $timezone)
	{
		$this->strategy = $strategy;
		$this->timezone = $timezone;
	}
	
	public function validate($reservationSeries)
	{
		JLog::add(JText::sprintf("COM_JONGMAN_LOG_START_VALIDATION", get_class($this)), JLog::INFO);
		$conflicts = array();
		$reservations = $reservationSeries->getInstances();
		foreach ($reservations as $reservation) {
			$existingItems = $this->strategy->getItemsBetween($reservation->startDate(), $reservation->endDate());
			foreach ($existingItems as $existingItem) {
				if (
					$existingItem->getStartDate()->equals($reservation->endDate()) ||
					$existingItem->getEndDate()->equals($reservation->startDate())
				)
				{
					continue;
				}
				
				if ($this->isInConflict($reservation, $reservationSeries, $existingItem))
				{
					JLog::add(JText::sprintf("COM_JONGMAN_LOG_RESERVATION_CONFLICT", $reservation->referenceNumber(), get_class($existingItem), $existingItem->getId()), 
						JLog::DEBUG);
					array_push($conflicts, $existingItem);
				}				
			}
		}
		
		$thereAreConflicts = count($conflicts) > 0;		
		
		if ($thereAreConflicts)
		{
			$this->setError($conflicts);
			return new RFReservationValidationResult(false, $this->getError());
		}
		
		return new RFReservationValidationResult();

	}
	protected function isInConflict(RFReservation $instance, RFReservationSeries $series, IReservedItem $existingItem)
	{
		return ($existingItem->getResourceId() == $series->resourceId()) ||
			(false !== array_search($existingItem->getResourceId(), $series->allResourceIds()));
	}

	/**
	 * @param array|IReservedItemView[] $conflicts
	 * @return string
	 */
	protected function setError($conflicts)
	{
		$format = 'Y-m-d H:i';
		
		$dates = array();
		$timezone = JongmanHelper::getUserTimezone();
		/** @var IReservedItemView $conflict */
		foreach($conflicts as $conflict)
		{
			$dates[] = $conflict->getStartDate()->toTimezone($timezone)->format($format)
				.' - '.$conflict->getEndDate()->toTimezone($timezone)->format($format);
		}
		
		$uniqueDates = array_unique($dates);
		sort($uniqueDates);
		
		$this->message[] = JText::plural("COM_JONGMAN_ERROR_RESERVATION_CONFLICT", count($uniqueDates));	

		foreach ($uniqueDates as $date)
		{
			$this->message[] = $date;
		}
		
	}
	
	public function getError()
	{
		return $this->message;
	}
}