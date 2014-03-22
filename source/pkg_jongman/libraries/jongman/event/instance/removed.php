<?php
defined('_JEXEC') or die;

class RFEventInstanceRemoved extends RFSeriesEvent
{
	/**
	 * @var Reservation
	 */
	private $instance;

	/**
	 * @return Reservation
	 */
	public function getInstance()
	{
		return $this->instance;
	}

	public function __construct(RFReservation $reservationInstance, RFReservationExistingseries $series)
	{
		$this->instance = $reservationInstance;
		parent::__construct($series, RFEventPriority::Highest);
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->instance->referenceNumber());
	}
}