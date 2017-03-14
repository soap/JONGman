<?php
defined('_JEXEC') or die;

class RFReservationRepository implements IReservationRepository
{
	public function loadById($reservationId)
	{
		JLog::add("RFReservationRepository::loadById() - ReservationID: $reservationId", JLog::DEBUG, 'info');

		$query = JFactory::getDbo()->getQuery(true);
		$query->select('*')
			->from('#__reservation_instances AS r')
			->join('inner', 'reservations AS rs ON r.reservation_id=rs.id')
			->where('r.id = '.$reservationId);
			//->where('status <> 2') ; //deleted
	
		return $this->load($query);
	}

	public function loadByReferenceNumber($referenceNumber)
	{
		JLog::add("RFReservationRepository::loadByReferenceNumber() - referenceNumber: $referenceNumber", JLog::DEBUG, 'info');

		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('*')
		->from('#__jongman_reservation_instances AS r')
		->join('inner', '#__jongman_reservations AS rs ON r.reservation_id=rs.id')
		->where('r.reference_number = '.$dbo->quote($referenceNumber));
		//->where('status <> 2') ; //deleted	
		return $this->load($query);
	}

	private function load($loadSeriesCommand)
	{
		$dbo = JFactory::getDbo();
		$dbo->setQuery($loadSeriesCommand);
		$rows = $dbo->loadObjectList();
		
		if ($rows === false || count($rows) == 0)
		{
			JLog::add('Reservation not found. ID');
			return null;
		}

		$series = $this->buildSeries($rows);
		$this->populateInstances($series);
		$this->populateResources($series);
		$this->populateParticipants($series);
		$this->populateAccessories($series);
		$this->populateAttributeValues($series);
		$this->populateAttachmentIds($series);
		$this->populateReminders($series);

		return $series;
	}

	public function add(RFReservationSeries $reservationSeries)
	{
		$dbo = JFactory::getDbo();

		$seriesId = $this->insertSeries($reservationSeries);

		$reservationSeries->setSeriesId($seriesId);

		$instances = $reservationSeries->instances();

		foreach ($instances as $reservation)
		{
			$command = new InstanceAddedEventCommand($reservation, $reservationSeries);
			$command->execute($database);
		}
	}

	/**
	 * Update existing reservation series  
	 * @param RFReservationExistingSeries $reservationSeries
	 */
	public function update(RFReservationExistingSeries $reservationSeries)
	{
		$database = JFactory::getDbo();
		$user = JFactory::getUser();
		if ($reservationSeries->requiresNewSeries())
		{
			$currentId = $reservationSeries->seriesId();
			$newSeriesId = $this->insertSeries($reservationSeries);
			JLog::add('Series branched from seriesId: %s to seriesId: %s', $currentId, $newSeriesId, JLog::DEBUG);

			$reservationSeries->setSeriesId($newSeriesId);

			/** @var $instance Reservation */
			foreach ($reservationSeries->instances() as $instance)
			{
				$instanceTable = JTable::getInstance('Instance', 'JongmanTable');
				$instanceData = array('reference_number' => $instance->referenceNumber(),
						'reservation_id' => $newSeriesId,
						'start_date' =>  $instance->startDate(), 'end_date' => $instance->endDate()
				);
				$instanceTable->save($instanceData);
			}
		}
		else
		{
			JLog::add(JText::sprintf('COM_JONGMAN_LOG_RESERVATION_UPDATE', $reservationSeries->seriesId()), JLog::DEBUG, 'debugging');
			$reservationTable = JTable::getInstance('Reservation', 'JongmanTable');
			$reservationTable->load($reservationSeries->seriesId());
			$reservationData = array('title' => $reservationSeries->getTitle(), 
					'description' =>$reservationSeries->getDescription(), 
					'repeat_type' =>$reservationSeries->getRepeatOptions()->repeatType(), 
					'repeat_options' => $reservationSeries->getRepeatOptions()->configurationString(), 
					'modified' => RFDate::now(),
					'modified_by' =>$user->id, 
					'state' => $reservationSeries->statusId(), 
					'owner_id' => $reservationSeries->userId(),
					'customer_id' => $reservationSeries->getCustomerId()
			);

			// Sav reservation data to database using JTable 
			$reservationTable->save($reservationData);

			/*
			 * 
			 foreach ($reservationSeries->addedAttachments() as $attachment)
			{
				$this->addReservationAttachment($attachment);
			}
			*/
		}

		$this->executeEvents($reservationSeries);
	}

	/**
	 * Insert new reservation series to database
	 * @param RFReservationSeries $reservationSeries
	 * @return int newly created series_id
	 */
	private function insertSeries(RFReservationSeries $reservationSeries)
	{
		$dbo = JFactory::getDbo();

		$table = JTable::getInstance('Reservation', 'JongmanTable');
		$reservationData = array('title'=> $reservationSeries->getTitle(), 
				'description'=>$reservationSeries->getDescription(),
				'repeat_type'=> $reservationSeries->getRepeatOptions()->repeatType(), 
				'repeat_options'=> $reservationSeries->getRepeatOptions()->configurationString(), 
				'type_id'=> RFReservationTypes::Reservation, 
				'state'=> $reservationSeries->statusId(), 
				'owner_id'=> $reservationSeries->userId()
		);
		
		$table->save($reservationData);
		$reservationSeriesId = $table->id;

		$reservationSeries->withSeriesId($reservationSeriesId);
		$resourceData = array('reservation_id'=> $reservationSeriesId,
				'resource_id' =>	$reservationSeries->resourceId(),
				'resource_level' => RFResourceLevel::Primary
		);
		
		$table = JTable::getInstance('Resource', 'JongmanTable');
		$table->save($resourceData);

		foreach ($reservationSeries->additionalResources() as $resource)
		{
			$table = JTable::getInstance('Resource', 'Jongman');
			$additionalResource = array('reservation_id'=> $reservationSeriesId,
				'resource_id'=>$resource->retResourceId(),
				'resource_level' => RFResourceLevel::Additional		
			);
			
			$table->save($additionalResource);
		}

		foreach ($reservationSeries->accessories() as $accessory)
		{
			//$insertAccessory = new AddReservationAccessoryCommand($accessory->accessoryId, $accessory->quantityReserved, $reservationSeriesId);
			//$database->Execute($insertAccessory);
		}

		$data = array();
		$data['reservation_custom_fields'] = array();
		foreach ($reservationSeries->attributeValues() as $attribute)
		{
			$data['reservation_custom_fields'][$attribute->attributeId] = $attribute->value; 
		}
		
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('extension');
		$isNew = true;
		$results = $dispatcher->trigger('onReservationSeriesAfterSave', array($data, $table, $results, $isNew));
		
		if (count($results) && in_array(false, $results, true)) {
			//$this->setError($dispatcher->getError());
			return false;
		}

		foreach ($reservationSeries->addedAttachments() as $attachment)
		{
			//$this->AddReservationAttachment($attachment);
		}

		if ($reservationSeries->getStartReminder()->enabled())
		{
			//$reminder = $reservationSeries->getStartReminder();
			//$insertAccessory = new AddReservationReminderCommand($reservationSeriesId, $reminder->MinutesPrior(), ReservationReminderType::Start);
			//$database->Execute($insertAccessory);
		}

		if ($reservationSeries->getEndReminder()->enabled())
		{
			//$reminder = $reservationSeries->getEndReminder();
			//$insertAccessory = new AddReservationReminderCommand($reservationSeriesId, $reminder->MinutesPrior(), ReservationReminderType::End);
			//$database->Execute($insertAccessory);
		}

		return $reservationSeriesId;
	}

	public function delete(RFReservationExistingSeries $existingReservationSeries)
	{
		$this->executeEvents($existingReservationSeries);
	}

	private function executeEvents(RFReservationExistingSeries $existingReservationSeries)
	{
		$database = JFactory::getDbo();
		$events = $existingReservationSeries->getEvents();
		
		foreach ($events as $event)
		{
			$command = $this->getReservationCommand($event, $existingReservationSeries);

			if ($command != null)
			{
				$command->execute($database);
			}
		}
	}

	/// LOAD BY ID HELPER FUNCTIONS

	/**
	 * @param IReader $reader
	 * @return ExistingReservationSeries
	 */
	private function buildSeries($rows)
	{
		$series = new RFReservationExistingSeries();
		if ($row = $rows[0])
		{
			$repeatType = $row->repeat_type; //[ColumnNames::REPEAT_TYPE];
			$configurationString = $row->repeat_options; //[ColumnNames::REPEAT_OPTIONS];

			$repeatOptions = $this->buildRepeatOptions($repeatType, $configurationString);
			$series->withRepeatOptions($repeatOptions);

			$seriesId = $row->reservation_id; //[ColumnNames::SERIES_ID];
			$title = $row->title; //[ColumnNames::RESERVATION_TITLE];
			$description = $row->description; //[ColumnNames::RESERVATION_DESCRIPTION];

			$series->withId($seriesId);
			$series->withTitle($title);
			$series->withDescription($description);
			$series->withOwner($row->owner_id);  //[ColumnNames::RESERVATION_OWNER]);
			$series->withStatus($row->state); //[ColumnNames::RESERVATION_STATUS]);

			$startDate = RFDate::fromDatabase($row->start_date); //[ColumnNames::RESERVATION_START]);
			$endDate = RFDate::fromDatabase($row->end_date); //[ColumnNames::RESERVATION_END]);
			$duration = new RFDateRange($startDate, $endDate);

			$instance = new RFReservation($series, $duration, $row->id, $row->reference_number);

			$series->withCurrentInstance($instance);
		}

		return $series;
	}

	private function populateInstances(RFReservationExistingSeries $series)
	{
		// get all series instances
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__jongman_reservation_instances AS a')
			->where('a.reservation_id = '.$series->seriesId())
			->order('a.start_date ASC');
		$db->setQuery($query);
		$rows = $db->loadObjectList();

		foreach ($rows as $row) {
			$duration = new RFDateRange(RFDate::fromDatabase($row->start_date), RFDate::fromDatabase($row->end_date));
			$instance = new RFReservation($series, $duration, $row->reservation_id, $row->reference_number);
			$series->withInstance($instance);
		}
	}

	private function populateResources(RFReservationExistingSeries $series)
	{
		// get all reservation resources
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('r.*, a.resource_level as resource_level')
			->from('#__jongman_reservation_resources AS a')
			->join('LEFT', '#__jongman_resources AS r ON r.id=a.resource_id')
			->where('a.reservation_id = '.$series->seriesId())
			->order('a.resource_level ASC, r.title ASC');
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		foreach($rows as $row) {
			$repeatConfig = new JRegistry($row->params);
			$resource = new RFResourceBookable(
								$row->id, $row->title, $row->location, $row->contact_info, $row->note,
								$repeatConfig->get('min_reservation_duration'), $repeatConfig->get('max_reservation_duration'),
								$repeatConfig->get('auto_assign'), $repeatConfig->get('need_approval'), $repeatConfig->get('overlap_day_reservation'),
								$repeatConfig->get('max_participants'), $repeatConfig->get('min_notice_time'), $repeatConfig->get('max_notice_time'),
								$row->description, $row->schedule_id, null 
							);
			if ($row->resource_level == 1) {
				$series->withPrimaryResource($resource);	
			}else{
				$series->withResource($resource);	
			}	
		}
	}

	private function populateParticipants(RFReservationExistingSeries $series)
	{
		$dbo = JFactory::getDbo();
		
		$query = $dbo->getQuery(true);
		
		$query->select('ru.user_id, ru.user_level')
			->from('#__jongman_reservation_users AS ru')
			->select('ri.*')
			->join('inner', '#__jongman_reservation_instances AS ri ON ru.reservation_instance_id = ri.id')
			->where('ri.reservation_id ='.$series->seriesId());
		
		$dbo->setQuery($query);
		$rows = $dbo->loadObjectList();
		foreach ($rows as $row)
		{
			if ($row->user_level ==  RFReservationUserLevel::PARTICIPANT)
			{
				$series->getInstance($row->reference_number)->withParticipant($row->user_id);
			}
			if ($row->user_level == RFReservationUserLevel::INVITEE)
			{
				$series->getInstance($row->reference_number)->withInvitee($row->user_id);
			}
		}
	}

	private function populateAccessories(RFReservationExistingSeries $series)
	{
		/* 
		  
		$getResourcesCommand = new GetReservationAccessoriesCommand($series->seriesId());
		$reader = ServiceLocator::GetDatabase()->Query($getResourcesCommand);
		while ($row = $reader->GetRow())
		{
			$series->withAccessory(new ReservationAccessory($row[ColumnNames::ACCESSORY_ID], $row[ColumnNames::QUANTITY]));
		}
		$reader->Free();
		*/
	}

	private function populateAttributeValues(RFReservationExistingSeries $series)
	{
		
		//RFCustomAttributeCategory::RESERVATION
		$data = new StdClass();
		$data->reservation_id = $series->seriesId();
		$context = 'com_jongman.reservationitem';
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('extension');

		$results = $dispatcher->trigger('onReservationSeriesPrepareData', array('com_jongman.reservationitem', $data));
		
		if (count($results) && in_array(false, $results, true)) {
			//$this->setError($dispatcher->getError());
			return false;
		}
		if (isset($data->reservation_custom_fields) && count($data->reservation_custom_fields)) {
			foreach($data->reservation_custom_fields as $k => $v)
			$series->withAttribute(new RFAttributeValue($k, $v));
		}
	}

	private function populateAttachmentIds(RFReservationExistingSeries $series)
	{
		/*
		$getAttachments = new GetReservationAttachmentsCommand($series->SeriesId());
		$reader = ServiceLocator::GetDatabase()->Query($getAttachments);
		while ($row = $reader->GetRow())
		{
			$series->WithAttachment($row[ColumnNames::FILE_ID], $row[ColumnNames::FILE_EXTENSION]);
		}
		$reader->Free();
		*/
	}

	private function populateReminders(RFReservationExistingSeries $series)
	{
		/*
		$getReminders = new GetReservationReminders($series->SeriesId());
		$reader = ServiceLocator::GetDatabase()->Query($getReminders);
		while ($row = $reader->GetRow())
		{
			$reminder = ReservationReminder::FromMinutes($row[ColumnNames::REMINDER_MINUTES_PRIOR]);
			if ($row[ColumnNames::REMINDER_TYPE] == ReservationReminderType::Start)
			{
				$series->WithStartReminder($reminder);
			}
			else
			{
				$series->WithEndReminder($reminder);
			}
		}
		$reader->Free();
		*/
	}

	private function buildRepeatOptions($repeatType, $configurationString)
	{
		$configuration = RFRepeatConfiguration::create($repeatType, $configurationString);
		$factory = new RFReservationRepeatOptionsFactory();
		return $factory->create($repeatType, $configuration->interval, $configuration->terminationDate,
				$configuration->weekdays, $configuration->monthlyType);
	}

	// LOAD BY ID HELPER FUNCTIONS END

	/**
	 * @param $attachmentFileId int
	 * @return ReservationAttachment
	 */
	public function loadReservationAttachment($attachmentFileId)
	{
		$command = new GetReservationAttachmentCommand($attachmentFileId);
		$reader = ServiceLocator::GetDatabase()->Query($command);

		if ($row = $reader->GetRow())
		{
			$fileId = $row[ColumnNames::FILE_ID];
			$extension = $row[ColumnNames::FILE_EXTENSION];
			$contents = ServiceLocator::GetFileSystem()->GetFileContents(Paths::ReservationAttachments() . "$fileId.$extension");
			$attachment = ReservationAttachment::Create($row[ColumnNames::FILE_NAME],
					$row[ColumnNames::FILE_TYPE],
					$row[ColumnNames::FILE_SIZE],
					$contents,
					$row[ColumnNames::FILE_EXTENSION],
					$row[ColumnNames::SERIES_ID]);
			$attachment->WithFileId($fileId);

			return $attachment;
		}

		return null;
	}

	/**
	 * @param $attachmentFile ReservationAttachment
	 * @return int
	 */
	public function addReservationAttachment(RFReservationAttachment $attachmentFile)
	{
		$command = new AddReservationAttachmentCommand($attachmentFile->FileName(), $attachmentFile->FileType(), $attachmentFile->FileSize(), $attachmentFile->FileExtension(), $attachmentFile->SeriesId());
		$id = ServiceLocator::GetDatabase()->ExecuteInsert($command);
		$extension = $attachmentFile->FileExtension();
		$attachmentFile->WithFileId($id);

		ServiceLocator::GetFileSystem()->Add(Paths::ReservationAttachments(), "$id.$extension",
		$attachmentFile->FileContents());

		return $id;
	}
	
	protected function getReservationCommand($event, $series)
	{
		return RFReservationEventMapper::getInstance()->map($event, $series);
	}
}

class RFReservationEventMapper
{
	private $buildMethods = array();
	private static $instance;

	private function __construct()
	{
		$this->buildMethods['RFEventSeriesDeleted'] = 'buildDeleteSeriesCommand';
		$this->buildMethods['RFEVentOwnerChanged'] = 'buildOwnerChangedCommand';

		$this->buildMethods['RFEventInstanceAdded'] = 'buildAddReservationCommand';
		$this->buildMethods['RFEventInstanceRemoved'] = 'buildRemoveReservationCommand';
		$this->buildMethods['RFEventInstanceUpdated'] = 'buildUpdateReservationCommand';

		$this->buildMethods['RFEventResourceRemoved'] = 'buildRemoveResourceCommand';
		$this->buildMethods['RFEventResourceAdded'] = 'buildAddResourceCommand';

		$this->buildMethods['RFEventAccessoryAdded'] = 'buildAddAccessoryCommand';
		$this->buildMethods['RFEventAccessoryRemoved'] = 'buildRemoveAccessoryCommand';

		$this->buildMethods['RFEventAttributeAdded'] = 'buildAddAttributeCommand';
		$this->buildMethods['RFEventAttributeRemoved'] = 'buildRemoveAttributeCommand';

		$this->buildMethods['RFEventAttachmentRemoved'] = 'buildAttachmentRemovedEvent';

		$this->buildMethods['RFEventReminderAdded'] = 'buildReminderAddedEvent';
		$this->buildMethods['RFEventReminderRemoved'] = 'buildReminderRemovedEvent';
	}

	/**
	 * @static
	 * @return RFReservationEventMapper
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new RFReservationEventMapper();
		}

		return self::$instance;
	}

	/**
	 * @param $event mixed
	 * @param $series ExistingReservationSeries
	 * @return EventCommand
	 */
	public function map($event, RFReservationExistingSeries $series)
	{
		$eventType = get_class($event);
		if (!isset($this->buildMethods[$eventType]))
		{
			//Log::Debug("No command event mapper found for event $eventType");
			return null;
		}
		
		$method = $this->buildMethods[$eventType];
		return $this->$method($event, $series);
	}

	private function buildDeleteSeriesCommand(RFEventSeriesDeleted $event)
	{
		return new RFEventCommandSeriesDeleted($event->series());
	}

	private function buildAddReservationCommand(RFEventInstanceAdded $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommandInstanceAdded($event->getInstance(), $series);
	}

	private function buildRemoveReservationCommand(RFEventInstanceRemoved $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommandInstanceRemoved($event->getInstance(), $series);
	}

	private function buildUpdateReservationCommand(RFEventInstanceUpdated $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommandInstanceUpdated($event->getInstance(), $series);
	}

	private function buildRemoveResourceCommand(RFEventResourceRemoved $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommandResourceRemoved($series->seriesId(), $event->resourceId());
	}

	private function buildAddResourceCommand(RFEventResourceAdded $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommandResourceAdded($series->seriesId(), $event->resourceId(), $event->resourceLevel());
	}

	private function buildAddAccessoryCommand(AccessoryAddedEvent $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommand(new AddReservationAccessoryCommand($event->accessoryId(), $event->quantity(), $series->seriesId()), $series);
	}

	private function buildRemoveAccessoryCommand(AccessoryRemovedEvent $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommand(new RemoveReservationAccessoryCommand($series-sSeriesId(), $event->AccessoryId()), $series);
	}

	private function buildAddAttributeCommand(RFEventAttributeAdded $event, RFReservationExistingSeries $series)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$values = $dbo->quote(array($series->seriesId(),'reservation_custom_fields.'.$event->attributeId(), $event->value()));
		
		$query->insert('#__jongman_reservation_fields')
			->columns($dbo->quoteName(array('reservation_id', 'field_key', 'field_value')))
			->values( implode(',', $values));
		return new RFEventCommand($query, $series);
	}

	private function buildRemoveAttributeCommand(RFEventAttributeRemoved $event, RFReservationExistingSeries $series)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->delete('#__jongman_reservation_fields')
			->where('reservation_id='.$series->seriesId())
			->where('field_key='.$dbo->quote('reservation_custom_fields.'.$event->attributeId()) );
		
		return new RFEventCommand($query, $series);
	}

	private function buildOwnerChangedCommand(RFEventOwnerChanged $event, RFReservationExistingSeries $series)
	{
		return new RFEventOwnerChangedCommand($event);
	}

	private function buildAttachmentRemovedEvent(AttachmentRemovedEvent $event, RFReservationExistingSeries $series)
	{
		return new RFAttachmentRemovedCommand($event);
	}

	private function buildReminderAddedEvent(ReminderAddedEvent $event, RFReservationExistingSeries $series)
	{
		return new ReminderAddedCommand($event);
	}

	private function buildReminderRemovedEvent(ReminderRemovedEvent $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommand(new RemoveReservationReminderCommand($series->SeriesId(), $event->ReminderType()), $series);
	}
}

interface IReservationRepository
{
	/**
	 * Insert a new reservation
	 *
	 * @param RFReservationSeries $reservation
	 * @return void
	 */
	public function add(RFReservationSeries $reservation);

	/**
	 * Return an existing reservation series
	 *
	 * @param int $reservationInstanceId
	 * @return RFReservationExistingSeries or null if no reservation found
	*/
	public function loadById($reservationInstanceId);

	/**
	 * Return an existing reservation series
	 *
	 * @param string $referenceNumber
	 * @return RFReservationExistingSeries or null if no reservation found
	*/
	public function loadByReferenceNumber($referenceNumber);

	/**
	 * Update an existing reservation
	 *
	 * @param RFReservationExistingSeries $existingReservationSeries
	 * @return void
	*/
	public function update(RFReservationExistingSeries $existingReservationSeries);

	/**
	 * Delete all or part of an existing reservation
	 *
	 * @param ExistingReservationSeries $existingReservationSeries
	 * @return void
	*/
	public function delete(RFReservationExistingSeries $existingReservationSeries);

	/**
	 * @abstract
	 * @param $attachmentFileId int
	 * @return ReservationAttachment
	*/
	public function loadReservationAttachment($attachmentFileId);

	/**
	 * @param $attachmentFile ReservationAttachment
	 * @return int
	*/
	public function addReservationAttachment(RFReservationAttachment $attachmentFile);
}
