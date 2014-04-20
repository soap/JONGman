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
	protected $statusId; 
	protected $userId; // reservation owner 's user id
	
	/**
	 * @var RFResourceBookable
	 */
	protected $resource;
	
	/**
	 * @var JUser
	 */
	protected $bookedBy; // person who place a reservation
	protected $instances = array();
	
	protected $_repeatOptions;
	private $_currentInstanceKey;
    /**
     * @var RFResourceBookable[]
     */
    protected $_additionalResources = array();
	
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
	 * @param array $data
	 */
	public function bind($data)
	{
		$this->userId = (int) $data['owner_id'];
		$this->resource = $data['resource'];
		$this->bookedBy = JFactory::getUser();
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

    public function instances()
    {
        return $this->instances;
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
			JLog::add("Add new instance for duration {$date->getBegin()}-{$date->getEnd()}", JLog::DEBUG);
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
	 * @param RFDateRange $reservationDate
	 * @return bool
	 */
	protected function instanceStartsOnDate(RFDateRange $reservationDate)
	{
		/** @var $instance RFReservation */
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
	 * @param Reservation $instance
	 * @return bool
	 */
	protected function isCurrent(RFReservation $instance)
	{
		return $instance->referenceNumber() == $this->currentInstance()->referenceNumber();
	}

	/**
	 * @param int $resourceId
	 * @return bool
	 */
	public function containsResource($resourceId)
	{
		return in_array($resourceId, $this->allResourceIds());
	}
	/**
	 * @param RFDateRange $reservationDate
	 * @return RFReservation newly created instance
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

    /**
     * @return string
     */
    protected function getCurrentKey()
	{
		return $this->_currentInstanceKey;
	}
	
	protected function getNewKey(RFReservation $reservation)
	{
		return $reservation->referenceNumber();
	}
	
	/**
	 * @param RFResourceBookable $resource
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
            // var RFResourceBookable
			$ids[] = $resource->getResourceId();
		}
		return $ids;
	}

	/**
	 * @return array|RFResourceBookable[]
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
	
	public function scheduleId()
	{
		return $this->resource->getScheduleId();
	}
	/**
	 * 
	 * set reservation status, calculate from resource property and user right
	 * @param int $newStatus
	 */
	public function setStatusId($newStatus)
	{
		$this->statusId = $newStatus;	
	}
	
	public function getStatusId()
	{
		return $this->statusId;
	}
	
	public function isMarkedForDelete($reservationId)
	{
		// this is a new series
		return false;
	}

	public function isMarkedForUpdate($reservationId)
	{
		// this is a new series
		return false;
	}	
}