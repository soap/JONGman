<?php
defined('_JEXEC') or die;

class RFEventCommandInstanceremoved extends RFEventCommand
{
	/**
	 * @var Reservation
	 */
	private $instance;
	
	/**
	 * @var ReservationSeries
	 */
	private $series;
	
	public function __construct(RFReservation $instance, RFReservationSeries $series)
	{
		$this->instance = $instance;
		$this->series = $series;
	}
	
	public function execute($dbo = null)
	{
		if ($dbo == null) $dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->delete('#__jongman_reservation_instances')
			->where('reference_number ='.$this->instance->referenceNumber());
		$dbo->setQuery($qury);
		return $dbo->execute();
	}	
}