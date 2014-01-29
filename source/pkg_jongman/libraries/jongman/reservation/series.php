<?php
defined('_JEXEC') or die;

class RFReservationSeries 
{
	private $userId;
	private $resource;
	private $bookedBy;
	
	private $_instances = array();
	private $_repeatOptions;
	
	public function __construct($userId, $resource, $reservationDate, $repeatOptions, $bookedBy)
	{
		$this->userId = $userId;
		$this->resource = $resource;
		
		$this->_repeatOptions = new RFReservationRepeatNone();
		$this->updateDuration($reservationDate);
		$this->repeats($repeatOptions);
		$this->bookedBy = $bookedBy;
	}
	
	public function getInstances()
	{
		return $this->_instances;	
	}
	
}