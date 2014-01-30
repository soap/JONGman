<?php
defined('_JEXEC') or die;

/**
 * Reservation series used to validate reservation conflict
 * @author Prasit Gebsaap
 *
 */
class RFReservationSeries 
{
	private $userId;
	private $resource;
	private $bookedBy;
	
	private $_instances = array();
	private $_repeatOptions;
	private $_currentInstanceKey;
	
	public function __construct()
	{
		$this->_repeatOptions = new RFReservationRepeatNone();
	}
	
	/**
	 * 
	 * bind data (from model 's form
	 * @param unknown_type $data
	 */
	public function bind($data)
	{
		$this->userId = $data['created_by'];
		$this->resource = $data['resource_id'];
		$this->bookedBy = JFactory::getUser((int)$data['owner_id']);
		$userTz = JFactory::getUser($this->userId)->getParam('timezone'); 
		$this->setDuration( 
			new RFDateRange(RFDate::parse($data['start_date'], $userTz), RFDate::parse($data['end_date'], $userTz))
				);

		$this->repeats($data['repeatOptions']);
		var_dump($this);
	}
	
	public function currentInstance()
	{
		$instance = $this->getInstance($this->getCurrentKey());
		if (!isset($instance))
		{
			throw new Exception("Current instance not found. Missing Reservation key {$this->GetCurrentKey()}");
		}
		return $instance;
	}
	
	public function getInstances()
	{
		return $this->_instances;	
	}
	
	public function getInstance($referenceNumber)
	{
		return $this->_instances[$referenceNumber];
	}
	
	protected function setDuration(RFDateRange $reservationDate)
	{
		$this->addNewCurrentInstance($reservationDate);
	}

	/**
	 * @param IRepeatOptions $repeatOptions
	 */
	protected function repeats(IRepeatOptions $repeatOptions)
	{
		$this->_repeatOptions = $repeatOptions;

		$dates = $repeatOptions->getDates($this->currentInstance()->duration());

		if (empty($dates))
		{
			return;
		}

		foreach ($dates as $date)
		{
			$this->addNewInstance($date);
		}
	}
	
	/**
	 * @param DateRange $reservationDate
	 * @return bool
	 */
	protected function instanceStartsOnDate(RFDateRange $reservationDate)
	{
		/** @var $instance Reservation */
		foreach ($this->_instances as $instance)
		{
			if ($instance->StartDate()->DateEquals($reservationDate->GetBegin()))
			{
				return true;
			}
		}
		return false;
	}
	
	/**
	 * @param DateRange $reservationDate
	 * @return Reservation newly created instance
	 */
	protected function addNewInstance(RFDateRange $reservationDate)
	{
		$newInstance = new RFReservation($this, $reservationDate);
		$this->addInstance($newInstance);

		return $newInstance;
	}

	protected function addNewCurrentInstance(RFDateRange $reservationDate)
	{
		$currentInstance = new RFReservation($this, $reservationDate);
		$this->addInstance($currentInstance);
		$this->setCurrentInstance($currentInstance);
	}

	protected function addInstance(RFReservation $reservation)
	{
		$key = $this->createInstanceKey($reservation);
		$this->_instances[$key] = $reservation;
	}

	protected function createInstanceKey(RFReservation $reservation)
	{
		return $this->getNewKey($reservation);
	}
	
	protected function setCurrentInstance(RFReservation $current)
	{
		$this->_currentInstanceKey = $this->getNewKey($current);
	}
	
	protected function getCurrentKey()
	{
		return $this->_currentInstanceKey;
	}
	
	protected function getNewKey(RFReservation $reservation)
	{
		return $reservation->referenceNumber();
	}
}