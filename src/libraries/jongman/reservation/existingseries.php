<?php
defined('_JEXEC') or die;
/**
 * 
 * Class to handle existing reservation series update
 * @author Prasit Gebsaap
 *
 */
class RFReservationExistingSeries extends RFReservationSeries
{
	/**
	 * @var ISeriesUpdateScope
	 */
	protected $seriesUpdateStrategy;

	/**
	 * @var array|SeriesEvent[]
	 */
	protected $events = array();

	/**
	 * @var array|int[]
	 */
	private $_deleteRequestIds = array();

	/**
	 * @var array|int[]
	 */
	private $_updateRequestIds = array();

	/**
	 * @var array|int[]
	 */
	private $_removedAttachmentIds = array();

	/**
	 * @var array|int[]
	 */
	protected $attachmentIds = array();
	
	/**
	 * @var array|ReservationAccessory[]
	 */
	protected $_accessories = array();
		
	
	public function __construct()
	{
		parent::__construct();
		// let all instances available first
		$this->applyChangesTo(RFReservationSeriesUpdatescope::FULLSERIES);
	}

	/**
	 * @return array|RFReservationAccessory[]
	 */
	public function accessories()
	{
		return $this->_accessories;
	}
	
	
	public function seriesUpdateScope()
	{
		return $this->seriesUpdateStrategy->getScope();
	}

	/**
	 * @internal
	 */
	public function withId($seriesId)
	{
		$this->setSeriesId($seriesId);
	}

	public function withBookedBy($bookedById)
	{
		$this->bookedBy = $bookedById;
	}
	/**
	 * @internal
	 */
	public function withOwner($userId)
	{
		$this->userId = $userId;
	}

	/**
	 * 
	 * @param int $customerId
	 */
	public function withCustomer($customerId)
	{
		$this->customerId = $customerId;
	}
	
	/**
	 * @internal
	 */
	public function withPrimaryResource(RFResourceBookable $resource)
	{
		$this->resource = $resource;
	}

	/**
	 * @internal
	 */
	public function withTitle($title)
	{
		$this->title = $title;
	}

	/**
	 * @internal
	 */
	public function withDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @internal
	 */
	public function withResource(RFResourceBookable $resource)
	{
		$this->addResource($resource);
	}

	/**
	 * @var IRepeatOptions
	 * @internal
	 */
	private $_originalRepeatOptions;

	/**
	 * @internal
	 */
	public function withRepeatOptions(IRepeatOptions $repeatOptions)
	{
		$this->_originalRepeatOptions = $repeatOptions;
		$this->_repeatOptions = $repeatOptions;
	}

	/**
	 * @internal
	 */
	public function withCurrentInstance(RFReservation $reservation)
	{
		$this->addInstance($reservation);
		$this->setCurrentInstance($reservation);
	}

	/**
	 * @internal
	 */
	public function withInstance(RFReservation $reservation)
	{
		$this->addInstance($reservation);
	}

	/**
	 * @param $statusId int|ReservationStatus
	 * @return void
	 */
	public function withStatus($statusId)
	{
		$this->statusId = $statusId;
	}

	/**
	 * @param ReservationAccessory $accessory
	 * @return void
	 */
	public function withAccessory(ReservationAccessory $accessory)
	{
		$this->_accessories[] = $accessory;
	}

	/**
	 * @param AttributeValue $attributeValue
	 */
	public function withAttribute(RFAttributeValue $attributeValue)
	{
		$this->addAttributeValue($attributeValue);
	}

	/**
	 * @param $fileId int
	 * @param $extension string
	 */
	public function withAttachment($fileId, $extension)
	{
		$this->attachmentIds[$fileId] = $extension;
	}

	/**
	 * @internal
	 */
	public function removeInstance(RFReservation $reservation)
	{
		if ($reservation == $this->currentInstance())
		{
			return; // never remove the current instance
		}

		$instanceKey = $this->getNewKey($reservation);
		unset($this->instances[$instanceKey]);

		$this->addEvent(new RFEventInstanceRemoved($reservation, $this));
		$this->_deleteRequestIds[] = $reservation->reservationId();
	}

	public function requiresNewSeries()
	{
		return $this->seriesUpdateStrategy->requiresNewSeries();
	}

	/**
	 * @return int|ReservationStatus
	 */
	public function statusId()
	{
		return $this->statusId;
	}

	/**
	 * @param int $userId
	 * @param RFResourceBookable $resource
	 * @param string $title
	 * @param string $description
	 * @param JUser $updatedBy
	 * @paran int $customerId
	 */
	public function update($userId, $resource, $title, $description, JUser $updatedBy, $customerId=null)
	{
		if ($this->resource->getId() != $resource->getId())
		{
			$this->addEvent(new RFEventResourceRemoved($this->resource, $this));
			$this->addEvent(new RFEventResourceAdded($resource, RFResourceLevel::Primary, $this));
		}

		if ($this->userId() != $userId)
		{
			$this->addEvent(new RFEventOwnerChanged($this, $this->userId(), $userId));
		}

		$this->customerId = $customerId;
		$this->userId = $userId;
		$this->resource = $resource;
		$this->title = $title;
		$this->description = $description;
		$this->bookedBy = $updatedBy;
	}
	
	/**
	 * @param RFDateRange $reservationDate
	 */
	public function updateDuration(RFDateRange $reservationDate)
	{
		$currentDuration = $this->currentInstance()->duration();

		if ($currentDuration->equals($reservationDate))
		{
			return;
		}

		$currentBegin = $currentDuration->getBegin();
		$currentEnd = $currentDuration->getEnd();

		$startTimeAdjustment = $currentBegin->getDifference($reservationDate->getBegin());
		$endTimeAdjustment = $currentEnd->getDifference($reservationDate->getEnd());

		JLog::add( JText::sprintf("COM_JONGMAN_LOG_RESERVATION_UPDATE_DURATION", $this->seriesId()), JLog::DEBUG, 'debug' );

		foreach ($this->getInstances() as $instance)
		{
			$newStart = $instance->startDate()->applyDifference($startTimeAdjustment);
			$newEnd = $instance->endDate()->applyDifference($endTimeAdjustment);

			$this->updateInstance($instance, new RFDateRange($newStart, $newEnd));
		}
	}

	/**
	 * @param SeriesUpdateScope|string $seriesUpdateScope
	 */
	public function applyChangesTo($seriesUpdateScope)
	{
		$this->seriesUpdateStrategy = RFReservationSeriesUpdateScope::createStrategy($seriesUpdateScope);

		if ($this->seriesUpdateStrategy->requiresNewSeries())
		{
			$this->addEvent(new RFEventSeriesBranched($this));
			$this->repeats($this->seriesUpdateStrategy->getRepeatOptions($this));
		}
	}

	/**
	 * @param IRepeatOptions $repeatOptions
	 */
	public function repeats(IRepeatOptions $repeatOptions)
	{
		if ($this->seriesUpdateStrategy->canChangeRepeatTo($this, $repeatOptions))
		{
			JLog::add("Updating recurrence for series {$this->seriesId()}", JLog::DEBUG, 'debugging');

			$this->_repeatOptions = $repeatOptions;

			foreach ($this->instances as $instance)
			{
				// delete all reservation instances which will be replaced
				if ($this->seriesUpdateStrategy->shouldInstanceBeRemoved($this, $instance))
				{
					$this->removeInstance($instance);
				}
			}
			
			// create all future instances
			parent::repeats($repeatOptions);
		}
	}

	/**
	 * @param $resources array|BookableResource([]
	 * @return void
	 */
	public function changeResources($resources)
	{
		$diff = new RFArrayDiff($this->_additionalResources, $resources);

		$added = $diff->getAddedToArray1();
		$removed = $diff->getRemovedFromArray1();

		/** @var $resource RFResourceBookable */
		foreach ($added as $resource)
		{
			$this->addEvent(new RFEventResourceAdded($resource, RFResourceLevel::Additional, $this));
		}

		/** @var $resource BookableResource */
		foreach ($removed as $resource)
		{
			$this->addEvent(new RFEventResourceRemoved($resource, $this));
		}

		$this->_additionalResources = $resources;
	}

	/**
	 * @param JUser $deletedBy
	 * @return void
	 */
	public function delete(JUser $deletedBy)
	{
		$this->bookedBy = $deletedBy;

		if (!$this->appliesToAllInstances())
		{
			$instances = $this->getInstances();
			JLog::add("Removing {count($instances)} instances of series {$this->seriesId()}", JLog::DEBUG);

			foreach ($instances as $instance)
			{
				JLog::add("Removing instance {$instance->referenceNumber()} from series $this->SeriesId()", JLog::DEBUG);
				$this->addEvent(new RFEventInstanceRemoved($instance, $this));
			}
		}
		else
		{
			JLog::add("Removing series {$this->seriesId()}", JLog::DEBUG);

			$this->addEvent(new RFEventSeriesDeleted($this));
		}
	}

	/**
	 * @param JUser $approvedBy
	 * @return void
	 */
	public function approve(JUser $approvedBy)
	{
		$this->updatedBy = $approvedBy;

		$this->statusId = RFReservationStatus::approved;

		//Log::Debug("Approving series %s", $this->SeriesId());

		$this->addEvent(new RFEventSeriesApproved($this));
	}

	/**
	 * @param JUser $approvedBy
	 * @return void
	 */
	public function reject(JUser $rejectedBy)
	{
		$this->updatedBy = $rejectedBy;
	
		$this->statusId = RFReservationStatus::rejected;
	
		//Log::Debug("Approving series %s", $this->SeriesId());
	
		$this->addEvent(new RFEventSeriesApproved($this));
	}
	/**
	 * @return bool
	 */
	private function appliesToAllInstances()
	{
		// compare existing instances with those from update scope 
		return count($this->instances) == count($this->getInstances());
	}

	protected function addNewInstance(RFDateRange $reservationDate)
	{
		if (!$this->instanceStartsOnDate($reservationDate))
		{
			//Log::Debug('Adding instance for series %s on %s', $this->seriesId(), $reservationDate);

			$newInstance = parent::addNewInstance($reservationDate);
			$this->addEvent(new RFEventInstanceAdded($newInstance, $this));
		}
	}

	/**
	 * @internal
	 */
	public function updateInstance(RFReservation $instance, RFDateRange $newDate)
	{
		unset($this->instances[$this->createInstanceKey($instance)]);

		$instance->setReservationDate($newDate);
		$this->addInstance($instance);

		$this->raiseInstanceUpdatedEvent($instance);

	}

	private function raiseInstanceUpdatedEvent(RFReservation $instance)
	{
		if (!$instance->isNew())
		{
			$this->addEvent(new RFEventInstanceUpdated($instance, $this));
			$this->_updateRequestIds[] = $instance->reservationId();
		}
	}

	/**
	 * @return array|RFSeriesEvent[]
	 */
	public function getEvents()
	{
		$uniqueEvents = array_unique($this->events);
		usort($uniqueEvents, array('RFSeriesEvent', 'compare'));

		return $uniqueEvents;
	}
	
	/**
	 * get all reservation instances
	 * @see RFReservationSeries::getInstances()
	 */
	public function getInstances()
	{
		return $this->seriesUpdateStrategy->getInstances($this);
	}

	/**
	 * @internal
	 * @deprecated 3.5
	 */
	public function _instances()
	{
		return $this->instances;
	}

	protected function addEvent(RFSeriesEvent $event)
	{
		$this->events[] = $event;
	}

	public function isMarkedForDelete($reservationId)
	{
		return in_array($reservationId, $this->_deleteRequestIds);
	}

	public function isMarkedForUpdate($reservationId)
	{
		return in_array($reservationId, $this->_updateRequestIds);
	}

	/**
	 * @param int[] $participantIds
	 * @return void
	 */
	public function changeParticipants($participantIds)
	{
		/** @var Reservation $instance */
		foreach ($this->getInstances() as $instance)
		{
			$numberChanged = $instance->changeParticipants($participantIds);
			if ($numberChanged != 0)
			{
				$this->raiseInstanceUpdatedEvent($instance);
			}
		}
	}

	/**
	 * @param int[] $inviteeIds
	 * @return void
	 */
	public function changeInvitees($inviteeIds)
	{
		/** @var Reservation $instance */
		foreach ($this->getInstances() as $instance)
		{
			$numberChanged = $instance->changeInvitees($inviteeIds);
			if ($numberChanged != 0)
			{
				$this->raiseInstanceUpdatedEvent($instance);
			}
		}
	}

	/**
	 * @param int $inviteeId
	 * @return void
	 */
	public function acceptInvitation($inviteeId)
	{
		/** @var Reservation $instance */
		foreach ($this->getInstances() as $instance)
		{
			$wasAccepted = $instance->acceptInvitation($inviteeId);
			if ($wasAccepted)
			{
				$this->raiseInstanceUpdatedEvent($instance);
			}
		}
	}

	/**
	 * @param int $inviteeId
	 * @return void
	 */
	public function declineInvitation($inviteeId)
	{
		/** @var Reservation $instance */
		foreach ($this->getInstances() as $instance)
		{
			$wasAccepted = $instance->declineInvitation($inviteeId);
			if ($wasAccepted)
			{
				$this->raiseInstanceUpdatedEvent($instance);
			}
		}
	}

	/**
	 * @param int $participantId
	 * @return void
	 */
	public function cancelAllParticipation($participantId)
	{
		/** @var Reservation $instance */
		foreach ($this->getInstances() as $instance)
		{
			$wasCancelled = $instance->cancelParticipation($participantId);
			if ($wasCancelled)
			{
				$this->raiseInstanceUpdatedEvent($instance);
			}
		}
	}

	/**
	 * @param int $participantId
	 * @return void
	 */
	public function cancelInstanceParticipation($participantId)
	{
		if ($this->currentInstance()->cancelParticipation($participantId))
		{
			$this->raiseInstanceUpdatedEvent($this->currentInstance());
		}
	}

	/**
	 * @param array|ReservationAccessory[] $accessories
	 * @return void
	 */
	public function changeAccessories($accessories)
	{
		$diff = new RFArrayDiff($this->_accessories, $accessories);

		$added = $diff->getAddedToArray1();
		$removed = $diff->getRemovedFromArray1();

		/** @var $accessory ReservationAccessory */
		foreach ($added as $accessory)
		{
			$this->addEvent(new RFEventAccessoryAdded($accessory, $this));
		}

		/** @var $accessory ReservationAccessory */
		foreach ($removed as $accessory)
		{
			$this->addEvent(new RFEventAccessoryRemoved($accessory, $this));
		}

		$this->_accessories = $accessories;
	}

	/**
	 * @param $attributes RFAttributeValue[]|array
	 */
	public function changeAttributes($attributes)
	{
		// Compare new attributes from user with existing ones
		$diff = new RFArrayDiff($this->_attributeValues, $attributes);

		$added = $diff->getAddedToArray1();
		$removed = $diff->getRemovedFromArray1();

		/** @var $attribute RFAttributeValue */
		foreach ($added as $attribute)
		{
			$this->addEvent(new RFEventAttributeAdded($attribute, $this));
		}

		/** @var $accessory RFAttributeValue */
		foreach ($removed as $attribute)
		{
			$this->addEvent(new RFEventAttributeRemoved($attribute, $this));
		}

		$this->_attributeValues = array();
		foreach ($attributes as $attribute)
		{
			$this->addAttributeValue($attribute);
		}
	}

	/**
	 * @param $fileId int
	 */
	public function removeAttachment($fileId)
	{
		$this->addEvent(new RFEventAttachmentRemoved($this, $fileId, $this->attachmentIds[$fileId]));
		$this->_removedAttachmentIds[] = $fileId;
	}

	/**
	 * @return array|int[]
	 */
	public function removedAttachmentIds()
	{
		return $this->_removedAttachmentIds;
	}

	public function addStartReminder(RFReservationReminder $reminder)
	{
		if ($reminder->minutesPrior() != $this->startReminder->minutesPrior())
		{
			$this->addEvent(new RFEventReminderAdded($this, $reminder->minutesPrior(), RFReservationReminderType::Start));
			parent::addStartReminder($reminder);
		}
	}

	public function addEndReminder(RFReservationReminder $reminder)
	{
		if ($reminder->minutesPrior() != $this->endReminder->minutesPrior())
		{
			$this->addEvent(new RFEventReminderAdded($this, $reminder->minutesPrior(), RFReservationReminderType::End));
			parent::addEndReminder($reminder);
		}
	}

	public function removeStartReminder()
	{
		if ($this->startReminder->enabled())
		{
			$this->startReminder = RFReservationReminder::None();
			$this->addEvent(new RFEventReminderRemoved($this, RFReservationReminderType::Start));
		}
	}

	public function removeEndReminder()
	{
		if ($this->endReminder->enabled())
		{
			$this->endReminder = RFReservationReminder::None();
			$this->addEvent(new RFEventReminderRemoved($this,RFReservationReminderType::End));
		}
	}

	public function withStartReminder(ReservationReminder $reminder)
	{
		$this->startReminder = $reminder;
	}

	public function withEndReminder(ReservationReminder $reminder)
	{
		$this->endReminder = $reminder;
	}
}
