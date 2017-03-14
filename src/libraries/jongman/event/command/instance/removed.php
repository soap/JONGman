<?php
defined('_JEXEC') or die;

class RFEventCommandInstanceRemoved extends RFEventCommand
{	
	/**
	 * @var Reservation
	 */
	private $instance;

	public function __construct(RFReservation $instance, RFReservationSeries $series)
	{
		$this->instance = $instance;
		$this->series = $series;
	}

	public function execute($dbo)
	{
		$table = JTable::getInstance('Instance', 'JongmanTable');
		if ($table->load(array('reference_number' => $this->instance->referenceNumber()) ))
		{
			$table->delete();
		}
	}
}