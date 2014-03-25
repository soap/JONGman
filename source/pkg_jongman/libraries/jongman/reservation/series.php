<?php
defined('_JEXEC') or die;

/**
 * Reservation series used to validate reservation conflict
 * @author Prasit Gebsaap
 *
 */
class RFReservationSeries extends JObject
{
	protected $seriesId; //reservation series id
	protected $title;
	protected $description; 
	protected $userId;
	
	/**
	 * @var RFResourceBookable
	 */
	protected $resource;
	
	/**
	 * @var JUser
	 */
	protected $bookedBy;
	protected $instances = array();
	
	protected $_repeatOptions;
	private $_currentInstanceKey;
	private $_additionalResources = array();
	
	public function __construct()
	{
		$this->_repeatOptions = new RFReservationRepeatNone();
	}
	
	/**
	 * @return int
	 */
	public function seriesId()
	{
		return $this->seriesId;
	}
	
	/**
	 * @param int $seriesId
	 */
	public function setSeriesId($seriesId)
	{
		$this->seriesId = $seriesId;
	}
	/**
	 * 
	 * bind data (from model 's form
	 * @param unknown_type $data
	 */
	public function bind($data)
	{
		$this->userId = JFactory::getUser()->get('id');
		$this->resource = $data['resource'];
		$this->bookedBy = JFactory::getUser((int)$data['owner_id']);
		$userTz = JFactory::getUser($this->userId)->getParam('timezone'); 
		$this->setDuration( 
			new RFDateRange(RFDate::parse($data['start_date'], $userTz), RFDate::parse($data['end_date'], $userTz))
				);

		$this->repeats($data['repeatOptions']);
	}
	
	/**
	 * 
	 * Get current reservation instance
	 * @throws Exception if cannot get current instance
	 * @return RFReservation
	 */
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
		return $this->instances;	
	}
	
	public function getInstance($referenceNumber)
	{
		return $this->instances[$referenceNumber];
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
	 * @return IRepeatOptions
	 */
	public function getRepeatOptions()
	{
		return $this->_repeatOptions;
	}
	
	/**
	 * @param DateRange $reservationDate
	 * @return bool
	 */
	protected function instanceStartsOnDate(RFDateRange $reservationDate)
	{
		/** @var $instance Reservation */
		foreach ($this->instances as $instance)
		{
			if ($instance->startDate()->dateEquals($reservationDate->getBegin()))
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
		$this->instances[$key] = $reservation;
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
	
	/**
	 * @param BookableResource $resource
	 */
	public function addResource(RFResourceBookable $resource)
	{
		$this->_additionalResources[] = $resource;
	}
	
	public function getResource()
	{
		return $this->resource;
	}
	
	public function resourceId()
	{
		return $this->resource->getResourceId();
	}
	/**
	 * @return int[]
	 */
	public function allResourceIds()
	{
		$ids = array($this->resourceId());
		foreach ($this->_additionalResources as $resource)
		{
			$ids[] = $resource->getResourceId();
		}
		return $ids;
	}

	/**
	 * @return array|BookableResource[]
	 */
	public function allResources()
	{
		return array_merge(array($this->resource), $this->_additionalResources);
	}
		
	public function userId()
	{
		return $this->userId;
	}
	
	/**
	 * Return user object who made a reservation record (may be not owner)
	 * @return JUser bookedBy
	 */
	public function bookedBy()
	{
		return $this->bookedBy;
	}
}