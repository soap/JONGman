<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

/**
 * Reservation series used to validate reservation conflict
 * @author Prasit Gebsaap
 *
 */
class RFReservationSeries extends JObject
{
	private $_currentInstanceKey;
	
	protected $seriesId; //reservation series id
	protected $title;
	protected $description;
	protected $statusId; 
	protected $userId; // reservation owner 's user id
	
	protected $customerId;
	/**
	 * @var RFResourceBookable
	 */
	protected $resource;
	
	/**
	 * @var JUser
	 */
	protected $bookedBy; 	// person who place a reservation
	protected $updatedBy; 	// person who update a reservation
	protected $instances = array();
	
	protected $_repeatOptions;
    /**
     * @var RFResourceBookable[]
     */
    protected $_additionalResources = array();
	
    protected $startReminder;
    protected $endReminder;

    protected $_attributeValues = array();
        
	public function __construct()
	{
		$this->_repeatOptions = new RFReservationRepeatNone();
		$this->startReminder = RFReservationReminder::None();
		$this->endReminder = RFReservationReminder::None();
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
	
	public function getTitle()
	{
		return $this->title;
	}
	
	public function getDescription()
	{
		return $this->description;
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
		if (isset($data['customer_id'])) $this->customerId = $data['customer_id'];
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
			throw new Exception("Current instance not found. Missing Reservation key {$this->getCurrentKey()}");
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
	 * @return int
	 */
	public function scheduleId()
	{
		return $this->resource->getScheduleId();
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
	 * @return array|BookableResource[]
	 */
	public function additionalResources()
	{
		return $this->_additionalResources;
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
	
	
	public function customerId()
	{
		return $this->getCustomerId();
	}
	
	public function getCustomerId()
	{
		return $this->customerId;
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
		return in_array($resourceId, $this->AllResourceIds());
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

    public function withSeriesId($seriesId)
    {
        $this->seriesId = $seriesId;
        /*foreach ($this->addedAttachments as $addedAttachment)
        {
            if ($addedAttachment != null)
            {
                $addedAttachment->WithSeriesId($seriesId);
            }
        }*/
    }
	/**
	 * @return RFReservationReminder
	 */
	public function getStartReminder()
	{
		return $this->startReminder;
	}
	
	/**
	 * @return RFReservationReminder
	 */
	public function getEndReminder()
	{
		return $this->endReminder;
	}
	
	public function addStartReminder(RFReservationReminder $reminder)
	{
		$this->startReminder = $reminder;
	}
	
	public function addEndReminder(RFReservationReminder $reminder)
	{
		$this->endReminder = $reminder;
	}
	
	public function addAttributeValue(RFAttributeValue $attributeValue)
	{
		$this->_attributeValues[$attributeValue->attributeId] = $attributeValue;
	}
	
	/**
	 * @param $customAttributeId
	 * @return mixed
	 */
	public function getAttributeValue($customAttributeId)
	{
		if (array_key_exists($customAttributeId, $this->_attributeValues))
		{
			return $this->_attributeValues[$customAttributeId]->value;
		}
	
		return null;
	}
	
	/* @since 3.0 */
	public function getAttributeValues()
	{
		return $this->_attributeValues;
	}
}