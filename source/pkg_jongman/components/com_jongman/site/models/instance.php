<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

// add field definitions from backend
JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields');
/**
 * Reservation model.
 *
 * @package     JONGman
 * @subpackage  Frontend
 * @since       1.0
 */
class JongmanModelInstance extends JModelAdmin
{
	
	protected static $series;
	protected $users = array();
	
	/**
	 * Method to get the Reservation form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 * @since   1.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			$this->option.'.'.$this->name,
			'reservation',
			array('control' => 'jform', 'load_data' => $loadData)
		);

		if (empty($form)) {
			return false;
		}

		return $form;
	}	

	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$table = $this->getTable('Instance', 'JongmanTable');
		$return = $table->load($pk);
			
		// Check for a table object error.
		if ($return === false && $table->getError())
		{
			$this->setError($table->getError());
			return false;
		}
		
		$series = $this->buildSeries($table->reference_number);
		$resutl = new JObject();
		$result->repeat_type = $series->getRepeatOptions()->repeatType();
		$result->repeat_options = new JRegistry($series->getRepeatOptions()->configurationString());
			
		$result->owner_id = $this->getOwnerId($table->reservation_id);
		$result->id = $table->id;
		$result->checked_out = 0;
		$result->instance_id = $table->id;
		$result->reference_number = $series->currentInstance()->referenceNumber(); 
		$result->resource_id = $series->resourceId();
		$result->schedule_id = $series->getResource()->getScheduleId();
		$result->title = $series->get('title');
		$result->description = $series->get('description');
		
		
		$date = new JDate($table->start_date);
		$result->start_date = $date->format('Y-m-d');
		$result->start_time = $date->format('H:i:s');
		
		$date = new JDate($table->end_date);
		$result->end_date = $date->format('Y-m-d');
		$result->end_time = $date->format('H:i:s');
		
		if ($result->repeat_type !== 'none') {
			$result->repeat_terminated = $series->getRepeatOptions()->terminationDate()->toDatabase();
			$result->repeat_interval = $result->repeat_options->get('repeat_interval');
		}
		if ($result->repeat_type == 'weekly') {
			$result->repeat_days = $result->repeat_options->get('repest_days');
		}
		if ($result->repeat_type == 'monthly') {
			$result->repeat_days = $result->repeat_options->get('repeat_days');
		}		
		
		return $result;
		
	}	
	
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->option.'.edit.'.$this->getName().'.data', array());
		if (empty($data)) {
			$data = $this->getItem();
			
		}
		
		return $data;
	}
	
	/**
	 * Delete reservation instance, delete reservation series if no instance exists
	 * @see JModelAdmin::delete()
	 */
	public function delete($pks)
	{
		// Initialise variables.
		$dispatcher = JDispatcher::getInstance();
		$pks = (array) $pks;
		$table = $this->getTable();
		$dbo = $this->getDbo();
		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('content');

		// Iterate the items to delete each one.
		foreach ($pks as $i => $pk) {
			if ($table->load($pk)){
				
				if ($this->canDelete($table)){
					$reservationId = $table->reservation_id;
					$context = $this->option . '.' . $this->name;

					// Trigger the onContentBeforeDelete event.
					$result = $dispatcher->trigger($this->event_before_delete, array($context, $table));
					if (in_array(false, $result, true))
					{
						$this->setError($table->getError());
						return false;
					}

					if (!$table->delete($pk))
					{
						$this->setError($table->getError());
						return false;
					}
					
					$query = $dbo->getQuery(true);
					$query->select(count('*'))
						->from('#__jongman_reservation_instances')
						->where('reservation_id = '.$reservationId);
					$dbo->setQuery($query);
					$count = $dbo->loadResult();
					if ($count == 0) {
						$reservationModel = JModel::getInstance('Reservation', 'JongmanModel', array('ignore_request'=>true));
						$config = array($reservationId);
						if (!$reservationModel->delete($config)) {
							return false;	
						}
					}
					
					
					// Trigger the onContentAfterDelete event.
					$dispatcher->trigger($this->event_after_delete, array($context, $table));

				}
				else
				{

					// Prune items that you can't change.
					unset($pks[$i]);
					$error = $this->getError();
					if ($error)
					{
						JError::raiseWarning(500, $error);
						return false;
					}
					else
					{
						JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
						return false;
					}
				}

			}
			else
			{
				$this->setError($table->getError());
				return false;
			}
		}

		// Clear the component's cache
		$this->cleanCache();

		return true;	
	}
	/**
	 * override to add resource reservation validation 
	 * @see JModelForm::validate()
	 */
	public function validate($form, $data, $group = null)
	{
		$updateScope = $data['updateScope'];
		
		$validData = parent::validate($form, $data, $group);
		if ($validData === false) return false;

		$input = $validData;
		$tz = JongmanHelper::getUserTimezone();

		$repeatType = $input['repeat_type'];
		$repeatInterval = $input['repeat_interval'];
		$weekDays = isset($input['repeat_days'])? $input['repeat_days'] : array();
		$monthyType = $input['repeat_monthly_type'];
		
		if (isset($input['repeat_terminated'])) {
			$terminated = RFDate::parse($input['repeat_terminated'], $tz);
			$terminated->setTime(new RFTime(0, 0, 0, $tz));
		}
		
		$existingSeries = $this->buildSeries($input['reference_number']);
		$input['repeatOptions'] = RFReservationRepeatOptionsFactory::create($repeatType, $repeatInterval, $terminated, $weekDays, $monthlyType);
		
		$row = JTable::getInstance('Resource', 'JongmanTable');
		$row->load($input['resource_id']);
		$resource = RFResourceBookable::create($row);
		
		$existingSeries->applyChangesTo($updateScope);
		$existingSeries->update($input['owner_id'], $resource,  $input['title'], $input['description'], JFactory::getUser());
						
		// calculate reseravtion status
		// @todo we should provide option to reset status if update reseravtion detai 
		$status = 1; //created
		foreach ($existingSeries->allResources() as $resource) {
			if ($resource->getRequiresApproval()) {
				if (!JongmanHelper::canApproveForResource($existingSeries->bookedBy(), $resource ))
				{
					$status = -1; //pending
					break;
				}	
			}	
		}
		$existingSeries->setStatusId($status);

		$input['start_date'] = $input['start_date'].' '.$input['start_time'];
		$input['end_date'] = $input['end_date'].' '.$input['end_time'];
		$duration = new RFDateRange(RFDate::parse($input['start_date'], $tz), RFDate::parse($input['end_date'], $tz));
				
		$existingSeries->updateDuration($duration);			
		$existingSeries->repeats($input['repeatOptions']);
		
		$config = array('ignore_request'=>true);
		$scheduleRepository = JModel::getInstance('Schedule', 'JongmanModel', $config);
		//start reservation validation here, get commone rules
		$ruleProcessor = JongmanHelper::getRuleProcessor();
		// Add specific rules for existing reservation validation
		$ruleProcessor->addRule(
					new RFValidationRuleExistingResourceAvailability(new RFResourceReservationAvailability($scheduleRepository), $tz), $existingSeries->bookedBy()
					);		
		$ruleProcessor->addRule(
					new RFValidationRuleResourceAvailability(new RFResourceBlackoutAvailability($scheduleRepository), $tz), $existingSeries->bookedBy()
					);
		/*
		$ruleProcessor->addRule(new ExistingResourceAvailabilityRule(new ResourceReservationAvailability($this->reservationRepository), $userSession->Timezone));
		$ruleProcessor->addRule(new AccessoryAvailabilityRule($this->reservationRepository, new AccessoryRepository(), $userSession->Timezone));
		$ruleProcessor->addRule(new ResourceAvailabilityRule(new ResourceBlackoutAvailability($this->reservationRepository), $userSession->Timezone));
		$ruleProcessor->addRule(new AdminExcludedRule(new ResourceMinimumDurationRule($this->resourceRepository), $userSession));
		$ruleProcessor->addRule(new AdminExcludedRule(new ResourceMaximumDurationRule($this->resourceRepository), $userSession));
		$ruleProcessor->addRule(new AdminExcludedRule(new QuotaRule(new QuotaRepository(), $this->reservationRepository, $this->userRepository, $this->scheduleRepository), $userSession));
		$ruleProcessor->addRule(new SchedulePeriodRule($this->scheduleRepository, $userSession));
		*/		
		$result = $ruleProcessor->validate($existingSeries);

		if (!$result->canBeSaved()) {
			$errors = $result->getErrors();
			foreach($errors as $error) {
				$this->setError($error);
			}
			return false;		
		}	
		
		// now we do our validation process
		return $validData;
	}

	/**
	 * Method to update existing reservation instance(s)
	 */
	public function update($data)
	{
		if ($this->series->requiresNewSeries())
		{
			$currentId = $this->series->seriesId();
			//insert new reservation
			$newData = $data;
			$newData['id'] = null;
			$newData['state'] = $this->series->statusId();
			$newData['repeat_options']= $this->series->getRepeatOptions()->configurationString();
			
			$newId = $this->insertSeries($newData);
			if ($newId === false) {
				$this->setError('COM_JONGMAN_ERROR_UPDATE_RESERVATION');
				return false;		
			}
			$this->series->withId($newId);
			
			$resourceIds = $this->series->allResourceIds();
			$dbo = $this->getDbo();
			foreach($resourceIds as $i => $resourceId) {
				$resource_level = ($i == 0 ? 0 : 1);
				$obj = new StdClass();
				$obj->reservation_id = (int) $newId;
				$obj->resource_id = $resourceId;
				$obj->resource_level = $resource_level;
				$dbo->insertObject('#__jongman_reservation_resources', $obj, 'id');
			}
			
			$table = $this->getTable();
			foreach ($this->series->getInstances() as $instance ) {
				//update instance
				$table->load(array('reference_number'=>$instance->referenceNumber()));
				$table->bind(array('reservation_id' => $newId,
						'reference_number' => $instance->referenceNumber(),
						'start_date'=> $instance->startDate()->toDatabase(),
						'end_date'=> $instance->endDate()->toDatabase()) 
					);
				$table->check();
				$table->store();
			}
		}else{
			// update new reservation series, no instances update
			$table = $this->getTable('Reservation', 'JongmanTable');
			$table->load($this->series->seriesId());
			$data = array(
				'title' => $this->series->get('title'),
				'description' => $this->series->get('description'),	
				'state'	=> $this->series->statusId(),
				'repeat_type' => $this->series->getRepeatOptions()->repeatType(),
				'repeat_options' => $this->series->getRepeatOptions()->configurationString()
			);
			if (!$table->bind($data)) {
				
			}
			
			if (!$table->check()) {
				
			}
			
			if (!$table->store()) {
				
			}	
		}
	
		$this->executeEvents($this->series);
		return true;		
	}
	
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name.
	 * @param   array   $config  Configuration array for model.
	 *
	 * @return  JTable  A database object
	 * @since   1.0
	 */
	public function getTable($type = 'Instance', $prefix = 'JongmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * 
	 * Build existing reservation series from database
	 * @param string $referenceNumber
	 * @return RFReservationExistingseries object
	 */
	public function buildSeries($referenceNumber) 
	{
		if (!empty($this->series)) return $this->series;
		
		$existingSeries = new RFReservationExistingseries();
		
		$keys = array('reference_number'=>$referenceNumber);
		$instance = $this->getTable();
		$instance->load($keys);
		
		$reservation = $this->getTable('Reservation', 'JongmanTable');			
		$reservation->load($instance->reservation_id);

		$factory = new RFReservationRepeatOptionsFactory();
		$repeatConfig = new JRegistry($reservation->repeat_options);
		$repeatTerminated = RFDate::fromDatabase($reservation->get('repeat_terminated'));
		$repeatOptions = $factory->create($reservation->repeat_type, 
							$repeatConfig->get('repeat_interval'), $repeatTerminated,
							$repeatConfig->get('repeat_days', array()),  
							$repeatConfig->get('repeat_monthly_type'));
		
		$existingSeries->withRepeatOptions($repeatOptions);
		$existingSeries->withId($instance->reservation_id);
		$existingSeries->withTitle($reservation->title);
		$existingSeries->withDescription($reservation->description);
		$existingSeries->withOwner($this->getOwnerId($instance->reservation_id));
		$existingSeries->withStatus($reservation->state);
		
		$startDate = RFDate::fromDatabase($instance->start_date);
		$endDate = RFDate::fromDatabase($instance->end_date);
		$duration = new RFDateRange($startDate, $endDate);
		$currentInstance = new RFReservation($existingSeries, $duration, $instance->reservation_id, $instance->reference_number);
		$existingSeries->withCurrentInstance($currentInstance);
		
		$this->populateResources($existingSeries);
		$this->populateInstances($existingSeries);
		// populate participants
		// populate accessories
		$this->series = $existingSeries;
		
		return $existingSeries;
	}
	
	protected function executeEvents($existingSeries = null)
	{
		if ($existingSeries == null) {
			$existingSeries = $this->series;
		}
		
		$events = $existingSeries->getEvents();
		$db = $this->getDbo();
		
		foreach ($events as $event) {
			$command = $this->getReservationCommand($event, $existingSeries);
			if ($command != null) {
				$command->execute($db);
			}		
		}
	}

	protected function insertSeries($data)
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable('Reservation', 'JongmanTable');
		$key = $table->getKeyName();
		$pk = null;

		$isNew = true;		
		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}
			// formulate data
			 
			// Bind the data.
			if (!$table->bind($data))
			{
				$this->setError($table->getError());
				return false;
			}

			// Prepare the row for saving
			$this->prepareTable($table);

			// Check the data.
			if (!$table->check())
			{
				$this->setError($table->getError());
				return false;
			}

			// Trigger the onContentBeforeSave event.
			$result = $dispatcher->trigger($this->event_before_save, array($this->option . '.' . $this->name, &$table, $isNew));
			if (in_array(false, $result, true))
			{
				$this->setError($table->getError());
				return false;
			}
			
			// Store the data.
			if (!$table->store())
			{
				$this->setError($table->getError());
				return false;
			}
			// Clean the cache.
			$this->cleanCache();

			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));

		}catch (Exception $e)
		{
			$this->setError($e->getMessage());

			return false;
		}
			
		$pkName = $table->getKeyName();

		if (isset($table->$pkName))
		{
			$this->setState($this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->getName() . '.new', $isNew);
		
		// insert reservation resources
		
		
		return $table->$pkName;
	}
	
	/**
	 * 
	 * Get owner id 
	 * @param int $reservationId
	 * @return int owner id
	 */
	protected function getOwnerId($reservationId)
	{
		if (!empty($this->users)) {
			return $this->users[1]->user_id;
		}
		$this->populateUsers($reservationId);

		return $this->users[1]->user_id;
	} 
	
	protected function populateUsers($reservationId)
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('user_id, user_level')
		->from('#__jongman_reservation_users')
		->where('reservation_id = '.(int)$reservationId);
		$db->setQuery($query);
		
		$this->users = $db->loadObjectList('user_level');
		
		return $this->users;		
	}
	
	/**
	 * 
	 * Populate reservation resources into existing series
	 * @param RFReservationExistingseries $series
	 * @since 2.0
	 */
	private function populateResources(RFReservationExistingseries $series)
	{
		$db = $this->getDbo();
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
	
	/**
	 * 
	 * Populate reservation instances into exisiting series
	 * @param RFReservationExistingseries $series
	 * @since 2.0
	 */
	private function populateInstances(RFReservationExistingseries $series)
	{
		$db = $this->getDbo();
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
	/**
	 * 
	 * Get JDatabaseQuery to be executed
	 * @param unknown $event
	 * @param unknown $series
	 * @return Ambigous <RFEventCommand, NULL>
	 */
	protected function getReservationCommand($event, $series)
	{
		return RFReservationEventMapper::getInstance()->map($event, $series);	
	}
}