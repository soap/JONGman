<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('jongman.base.ireservationsaveresultview');
jimport('jongman.cms.reservation.repository');
jimport('jongman.cms.reservation.model.*');
jimport('jongman.base.irepeatoptionscomposite');

// add field definitions from backend
JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields');
/**
 * Reservation model.
 *
 * @package     JONGman
 * @subpackage  Frontend
 * @since       1.0
 */
class JongmanModelInstance extends JModelAdmin implements IReservationPage, IReservationSaveResultView, IRepeatOptionsComposite
{
	private $_handler;
	private $_repository;
	private $_saveresult = false;
	private $_warnings = array();
	private $_saveSuccessfully = false;
	protected static $series;
	protected $validData = array();
	protected $users = array();
	
	/**
	 * 
	 * @param array $config
	 */
	public function __construct($config=array())
	{
		parent::__construct($config);
		$this->_repository = new RFReservationRepository();
		
	}
	
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
		$result = new JObject();
		$result->repeat_type = $series->getRepeatOptions()->repeatType();
		$result->repeat_options = new JRegistry($series->getRepeatOptions()->configurationString());
			
		$result->owner_id = $series->userId();
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
		
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('extension');
		$results = $dispatcher->trigger('onReservationSeriesPrepareData', array('com_jongman.reservation', $result));
		 
		if (count($results) && in_array(false, $results, true)) {
		 	$this->setError($dispatcher->getError());
		 	return false;
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
	 * (non-PHPdoc)
	 * @see JModelForm::preprocessForm()
	 */
	protected function preprocessForm(JForm $form, $data, $group = 'extension')
	{
		$params = JComponentHelper::getParams('com_jongman');
		$proxyReservation = (bool)$params->get('proxyReservation', false);
		if (!$proxyReservation) {
			$form->setFieldAttribute('owner_id', 'disabled', 'true');	
			$form->setFieldAttribute('owner_id', 'readonly', 'true');	
		}
		
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);
		$dispatcher = JEventDispatcher::getInstance();
		
		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onReservationSeriesPrepareForm', array($form, $data));
		
		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();
		
			if (!($error instanceof Exception))
			{
				throw new Exception($error);
			}
		}
		
		//parent::preprocessForm($form, $data, $group);		
	}
	
	public function update2($data) 
	{
		$this->validData = $data;
		$user = JFactory::getUser();
		$reservationAction = RFReservationAction::Update;

		$persistenceFactory = new RFFactoryReservationPersistence();
		$persistenceService = $persistenceFactory->create($reservationAction);
		$handler = $this->getHandler($reservationAction);
		$resourceRepository = new RFResourceRepository();
		
		$model = new RFReservationModelUpdate($this, $persistenceService, $handler, $resourceRepository, $user);
		
		$reservationSeries = $model->buildReservation();
		$model->handleReservation($reservationSeries);
		
		if (!$this->_saveresult) {
			// error already set in $this->setErrors(), get called in RFReservationModelUpdate class
			return false;
		}
		
		return true;
		
	}
	
	/**
	 * @since 3.0.0
	 * @see JModelAdmin::delete()
	 */
	public function delete(&$data)
	{
		$this->validData = $data;
		
		$user = JFactory::getUser();
		$reservationAction = RFReservationAction::Delete;
		
		$persistenceFactory = new RFFactoryReservationPersistence();
		$persistenceService = $persistenceFactory->create($reservationAction);

		$handler = $this->getHandler($reservationAction);

		$model = new RFReservationModelDelete($this, 
					$persistenceService, 
					$handler, 
					$user);

		$reservationSeries = $model->buildReservation();
		$model->handleReservation($reservationSeries);
		
		if (!$this->_saveresult) {
			// error already set in $this->setErrors(), get called in RFReservationModelDelete class
			return false;	
		}

		return true;
	}
	
	
	/**
	 * 
	 * @param unknown $reservationAction
	 * @param unknown $data
	 * @return RFReservationExistingSeries
	 */
	public function buildReservation($reservationAction, $data)
	{
		if ($reservationAction == RFReservationAction::Create) {
			
		}else if ($reservationAction == RFReservationAction::Update) {
			
		}else if ($reservationAction == RFReservationAction::Delete) {
			$referenceNumber = $data['reference_number'];
			$table = JTable::getInstance('Instance', 'JongmanTable');
			$table->load(array('reference_number'=>$referenceNumber));
			
			$series = $this->buildSeries($referenceNumber);
			$series->applyChangesTo($data['updatescope']);
			
			return $series;
		}else{
			
		}
				
	}
	
	/**
	 * Delete reservation instance, delete reservation series if no instance exists
	 * @deprecated 3.0.0
	 */
	public function handleReservation($data)
	{
		jimport('jongman.cms.reservation.repository');
		// Initialise variables.
		$user = JFactory::getUser();
		$dispatcher = JDispatcher::getInstance();
		$dbo = $this->getDbo();
		
		// Include the content plugins for the on delete events.
		JPluginHelper::importPlugin('content');

		$pk = $data['id'];
		$table = $this->getTable('Instance', 'JongmanTable');
		$return = $table->load($pk);
		
		// Check for a table object error.
		if ($return === false && $table->getError())
		{
			$this->setError($table->getError());
			return false;
		}
		
		$existingSeries = $this->buildSeries($table->reference_number);
		$existingSeries->applyChangesTo($data['updatescope']);
		// Get common rules
		$ruleProcessor = JongmanHelper::getRuleProcessor();
		// Add specific reservation delete rules
		$ruleProcessor->addRule(new RFReservationRuleAdminexcluded(new RFReservationRuleUserIsOwner($user), $user));
		$result = $ruleProcessor->validate($existingSeries);
		
		if (!$result->canBeSaved()) {
			$errors = $result->getErrors();
			foreach($errors as $error) {
				$this->setError($error);
			}
			return false;		
		}
		
		//mark instances as deleted or series as delete if it is single instanc
		$existingSeries->delete($user); 
		$this->_repository->delete($existingSeries);	
				
		// Clear the component's cache
		$this->cleanCache();

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
	 * @return RFReservationExistingSeries object
	 */
	public function buildSeries($referenceNumber) 
	{
		if (!empty($this->series)) return $this->series;
		
		$existingSeries = new RFReservationExistingSeries();
		
		$keys = array('reference_number'=>$referenceNumber);
		$instance = $this->getTable();
		$instance->load($keys);
		
		$reservation = $this->getTable('Reservation', 'JongmanTable');			
		$reservation->load($instance->reservation_id);

		$factory = new RFReservationRepeatOptionsFactory();
		$repeatConfig = new JRegistry($reservation->repeat_options);
		$repeatTerminated = RFDate::fromDatabase($repeatConfig->get('repeat_terminated'));
		$repeatOptions = $factory->create($reservation->repeat_type, 
							$repeatConfig->get('repeat_interval'), $repeatTerminated,
							$repeatConfig->get('repeat_days', array()),  
							$repeatConfig->get('repeat_monthly_type'));
		
		$existingSeries->withRepeatOptions($repeatOptions);
		$existingSeries->withId($instance->reservation_id);
		$existingSeries->withTitle($reservation->title);
		$existingSeries->withDescription($reservation->description);
		$existingSeries->withOwner($reservation->owner_id);
		$existingSeries->withStatus($reservation->state);
		
		$startDate = RFDate::fromDatabase($instance->start_date);
		$endDate = RFDate::fromDatabase($instance->end_date);
		$duration = new RFDateRange($startDate, $endDate);
		$currentInstance = new RFReservation($existingSeries, $duration, $instance->reservation_id, $instance->reference_number);
		$existingSeries->withCurrentInstance($currentInstance);
		
		$this->populateResources($existingSeries);
		$this->populateInstances($existingSeries);
		//$this->populateUsers($existingSeries);
		// populate participants
		// populate accessories
		self::$series = $existingSeries;
		
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
 
	
	protected function populateUsers(RFReservationExistingSeries $series)
	{
		$db = $this->getDbo();
		
		$query = $db->getQuery(true);
		$query->select('user_id, user_level')
		->from('#__jongman_reservation_users')
		->where('reservation_id = '.(int)$series);
		$db->setQuery($query);
		
		$this->users = $db->loadObjectList('user_level');
		
		return $this->users;		
	}
	
	/**
	 * 
	 * Populate reservation resources into existing series
	 * @param RFReservationExistingSeries $series
	 * @since 2.0
	 */
	private function populateResources(RFReservationExistingSeries $series)
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
	 * @param RFReservationExistingSeries $series
	 * @since 2.0
	 */
	private function populateInstances(RFReservationExistingSeries $series)
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
	
	/**
	 * (non-PHPdoc)
	 * @see JModelAdmin::canDelete()
	 */
	protected function canDelete($record)
	{
		$user = JFactory::getUser();
		
		if ($user->authorise('core.admin', 'com_jongman')) return true;
		
		$reservationId = $record->reservation_id;
		// Validate instance date time first
		
		$reservation = JTable::getInstance('Reservation', 'JongmanTable');
		
		if (!$reservation->load($reservationId)) {
			return $user->authorise('core.delete', 'com_jongman');
		}
		
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('resource_id')
				->from('#__jongman_reservation_resources')
				->where('reservation_id='.(int)$reservationId);
		$db->setQuery($query);
		$resources = $db->loadObjectList();
		
		if ($reservation->owner_id == $user->id || $reservation->created_by == $user->id) {
			if ($user->authorise('com_jongman.delete.own', 'com_jongman.resource.'.$resources[0]->resource_id)) {
				return true;
			}else{
				if ($user->authorise('com_jongman.delete.own', 'com_jongman')) return true; 
			}	
		}
		
		if ($user->authorise('core.delete', 'com_jongman.resource.'.$resources[0]->resource_id)) {
			return true;
		}else if ($user->authorise('core.delete','com_jongman')) {
			return true;
		}
		
		return false;
	}
	
	
	/**
	 *
	 * Get reservation handler based on Reservation Action to handle reservation request
	 * @param RFReservationAction $reservationAction
	 * @return IReservationHandler
	 * @since JONGman 3.0
	 */
	protected function getHandler($reservationAction)
	{
		$user = JFactory::getUser();
		/* we should set userSession properties!!! */
	
		$persistenceFactory = new RFFactoryReservationPersistence();
		$persistenceService = $persistenceFactory->create($reservationAction);
	
		$handler = RFReservationHandler::create($reservationAction, $persistenceService, $user);
	
		return $handler;
	}
	
	/** =========================== =========================================== **/
	
	public function getParticipants()
	{
		
	}
	
	
	public function getAccessories()
	{
		
	}
	
	public function getInvitees()
	{
		
	}
	
	public function getAttributes()
	{
		
	}
	
	public function getAttachments()
	{
		
	}
	
	public function getRemovedAttachmentIds()
	{
		
	}
	
	public function hasStartReminder()
	{
		
	}
	
	public function hasEndReminder()
	{
	
	}
	
	/** ============ IRepeatOptionsComposite ================================== **/
	/**
	 * @abstract
	 * @return string
	 */
	public function getRepeatType()
	{
		return $this->validData['repeat_type'];
	}
	
	/**
	 * @abstract
	 * @return string|null
	*/
	public function getRepeatInterval()
	{
		return $this->validData['repeat_interval'];
	}
	
	/**
	 * @abstract
	 * @return int[]|null
	*/
	public function getRepeatWeekdays()
	{
		return $this->validData['repeat_days'];
	}
	
	/**
	 * @abstract
	 * @return string|null
	*/
	public function getRepeatMonthlyType()
	{
		return $this->validData['repeat_monthly_type'];
	}
	
	/**
	 * @abstract
	 * @return string|null
	*/
	public function getRepeatTerminationDate()
	{
		return $this->validData['repeat_terminated'];
	}
	
	/** ===== ============================================= **/
	public function getReferenceNumber()
	{
		$referenceNumber = $this->validData['reference_number'];	
		return $referenceNumber;
	}
	
	public function getSeriesUpdateScope()
	{
		return $this->validData['updatescope'];	
	}
	
	/** ====== IReservationPage =========================== **/
	
	public function getUserId() 
	{
		return $this->validData['owner_id'];	
	}
	
	/**
	 * @return int
	 */
	public function getResourceId()
	{
		return $this->validData['resource_id'];	
	}
	
	/**
	 * @return string
	*/
	public function getTitle()
	{
		return $this->validData['title'];	
	}
	
	/**
	 * @return string
	*/
	public function getDescription()
	{
		return $this->validData['description'];
	}
	
	/**
	 * @return string
	*/
	public function getStartDate()
	{
		return $this->validData['start_date'];
	}
	
	/**
	 * @return string
	*/
	public function getEndDate()
	{
		return $this->validData['end_date'];
	}
	
	/**
	 * @return string
	*/
	public function getStartTime()
	{
		return $this->validData['start_time'];
	}
	/**
	 * @return string
	*/
	public function getEndTime()
	{
		return $this->validData['end_time'];
	}
	
	/**
	 * @return int[]
	*/
	public function getResources()
	{
		return array();	
	}

	/** ====== IReservationSaveResultView ================= **/
	/**
	 * @param bool $succeeded
	 */
	public function setSaveSuccessfulMessage($succeeded)
	{
		$this->_saveresult = $succeeded;
	}
	
	/**
	 * @param array|string[] $errors
	*/
	public function setErrors($errors)
	{
		foreach($errors as $error) {
			$error = trim ($error);
			if (!empty($error)) $this->setError($error);	
		}
	}
	
	/**
	 * @param array|string[] $warnings
	*/
	public function setWarnings($warnings)
	{
		array_push($this->_warnings, $warnings);	
	}
	
}