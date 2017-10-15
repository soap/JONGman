<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access
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
class JongmanModelReservation extends JModelAdmin
{
	protected $_resources = null;

	protected $_schedules = null;
	
	protected $_series = null; 
	
	protected $_actionModel = null;
	
	/**
	 * 
	 * @param array $config
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		if (isset($config['action'])) {
			switch ($config['$action']) {
				case RFReservationAction::Create :
					break;
				case RFReservationAction::Save :
						//Save new reservation
						$this->_actionModel = new RFReservationModelSave();
					break;
				case RFReservationAction::Update :
					$this->_actionModel = new RFReservationModelUpdate();
					break;
				case RFReservationAction::Delete :
					$this->_actionModel = new RFReservationModelDelete();
					break;
				case RFReservationAction::Approve :
					$this->_actionModel = new RFReservationModelApprove();
					break;
				default:
					break;	
			}					
		}
	}

	/**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     *
     * @return    void
     */
    protected function populateState()
    {
        $input = JFactory::getApplication()->input;
        // Load state from the request.
        $pk = $input->getInt('id');
        $this->setState($this->option .'.' .$this->getName() . '.id', $pk);
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
			$this->getName(),
			array('control' => 'jform', 'load_data' => $loadData)
		);

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a Reservation.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed    data object (JObject) on success, false on failure.
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		
		$dispatcher = JDispatcher::getInstance();
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$tz = RFApplicationHelper::getUserTimezone();
		$data = JFactory::getApplication()->getUserState($this->option.'.edit.'.$this->getName().'.data', array());
		if (empty($pk)){
			if (empty($data)) {
				$result = parent::getItem($pk);
			
				$result->instance_id = null;
				$result->schedule_id = $app->input->getInt('schedule_id');
				$result->resource_id = $app->input->getInt('resource_id');

				// it will be conveted to user time zone in form (by field)
				$date = JFactory::getDate($app->input->getString('start'), $tz);
				$date->setTimezone(new DateTimeZone('UTC'));
				$result->start_date 	= $date->format('Y-m-d');
				$result->start_time 	= $date->format('H:i:s');
				$result->repeat_type 	= 'none';
				$result->repeat_options = new JRegistry();
			
				// it will be converted to user time zone in form (by field)
				$date = JFactory::getDate($app->input->getString('end'), $tz);
				$date->setTimezone(new DateTimeZone('UTC'));
				$result->end_date 	= $date->format('Y-m-d');
				$result->end_time 	= $date->format('H:i:s');
			
				$result->owner_id 	= $user->id;
				$result->created_by = $user->id;
			}else{
				$result = parent::getItem($pk);	
				$result->instance_id	= null;
				$result->schedule_id 	= $data['schedule_id'];
				$result->owner_id 		= $user->id;
				$result->start_date 	= $data['start_date'];
				$result->end_date 		= $data['end_date'];
				$result->repeat_type 	= $data['repeat_type'];
				$result->repeat_options	= new JRegistry();
			}
			
			// No need to load data for custom fields as it is a new record
			/*
			JPluginHelper::importPlugin('extension');
			$results = $dispatcher->trigger('onReservationSeriesPrepareData', array('com_jongman.reservation', $item));
			if (count($results) && in_array(false, $results, true)) {
				$this->setError($dispatcher->getError());
				$item = new StdClass();
			}
			*/
			return $result;	
		}
		// we are not process existing reservation in this model
		$result = new StdClass();
		return $result;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string   $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name.
	 * @param   array   $config  Configuration array for model.
	 *
	 * @return  JTable  A database object
	 * @since   1.0
	 */
	public function getTable($type = 'Reservation', $prefix = 'JongmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   1.0
	 */
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
	}
		
	/**
	 * override to add resource reservation validation 
	 * @see JModelForm::validate()
	 */
	public function validate($form, $data, $group = null)
	{
	    if (isset($data['repeat_terminated']) ) {
	        $data['repeat_terminated'] .= ' 23:59:59';
        }
		$validData = parent::validate($form, $data, $group);
		if ($validData === false) return false;

		/**  @todo split this part and put it in controller? */
		$input = $validData;
		var_dump($input);
		$input['start_date'] = $input['start_date'].' '.$input['start_time'];
		$input['end_date'] = $input['end_date'].' '.$input['end_time'];
		$tz = RFApplicationHelper::getUserTimezone();
		
		$repeatType = isset($input['repeat_type']) ? $input['repeat_type'] : null;
		$repeatInterval = isset($input['repeat_interval']) ? $input['repeat_interval'] : null;
		$weekDays = isset($input['repeat_days']) ? $input['repeat_days'] : null;
		$monthlyType = isset($input['repeat_monthly_type']) ? $input['repeat_monthly_type'] : 'none';
		
		if (isset($input['repeat_terminated'])) {
			$terminated = RFDate::parse($input['repeat_terminated'], $tz);
			// fixed: last date reservation was not made [2017-03-16]
			$terminated = $terminated->setTime(new RFTime(23, 59, 59, $tz));
		}

		$input['repeatOptions'] = JongmanHelper::getRepeatOptions($repeatType, $repeatInterval, $terminated, $weekDays, $monthlyType);
		if (isset($input['resource_id']) && ($input['resource_id'] > 0)) {
			$row = JTable::getInstance('Resource', 'JongmanTable');
			$row->load($data['resource_id']);
			$input['resource'] = RFResourceBookable::create($row); 
		}
		
		$reservationSeries = new RFReservationSeries();
		$reservationSeries->bind($input);
		// calculate reseravtion status 
		$status = 1; //created
		foreach ($reservationSeries->allResources() as $resource) {
			if ($resource->getRequiresApproval()) {
				if (!JongmanHelper::canApproveForResource($reservationSeries->bookedBy(), $resource ))
				{
					$status = -1; //pending
					break;
				}	
			}	
		}
		$reservationSeries->setStatusId($status);
		
		//start reservation validation here, get commone rules
		$ruleProcessor = JongmanHelper::getRuleProcessor();
		// Add specific rules for new reservation validation
		
		$scheduleRepository = new RFScheduleRepository();
		
		$ruleProcessor->addRule(
					new RFReservationRuleExistingResourceAvailability( new RFResourceReservationAvailability($scheduleRepository), $tz ), 
					$reservationSeries->bookedBy()
				);	
		
		$ruleProcessor->addRule(
					new RFReservationRuleResourceAvailability(new RFResourceBlackoutAvailability($scheduleRepository), $tz), 
					$reservationSeries->bookedBy()
				);
		
		$ruleProcessor->addRule(
					new RFReservationRuleSchedulePeriod( $scheduleRepository, $reservationSeries->bookedBy() ) 
				);
		
		$quotaRepository = new RFQuotaRepository();
		$scheduleRepository = new RFScheduleRepository();
		$reservationViewRepository = new RFReservationViewRepository();
		$userRepository = new RFUserRepository();
		
		$ruleProcessor->addRule(
				new RFReservationRuleAdminexcluded(new RFReservationRuleQuota( $quotaRepository, $reservationViewRepository, $userRepository, $scheduleRepository), $reservationSeries->bookedBy())
			);
		
		$result = $ruleProcessor->validate($reservationSeries);
		if (!$result->canBeSaved()) {
			$errors = $result->getErrors();
			foreach($errors as $error) {
				$this->setError($error);
			}
			
			return false;		
		}			
		
		$this->_series = $reservationSeries;
		$validData['series'] = $reservationSeries;
		$validData['repeat_options'] = $reservationSeries->getRepeatOptions()->configurationString();

		return $validData;
	}
	
	/**
	 * Method to save the new reservation data.
	 * @param   array  $data  The form data.
	 * @return  boolean  True on success, False on error.
	 */
	public function save($data)
	{
	    // save reservation data first
		$result = $this->insertSeries($data);
		if ($result == false) {
			return false;
		}

		// then save reservation instance
		$instances = $this->_series->getInstances();
		foreach ($instances as $instance) {
			$instanceTable = JTable::getInstance('Instance', 'JongmanTable');
			$instance->setReservationId( $result );
			$src = array('reference_number' => $instance->referenceNumber(),
					'start_date' => $instance->startDate()->toDatabase(),
					'end_date'=> $instance->endDate()->toDatabase(),
					'reservation_id' => (int) $result
				);
			
			$instanceTable->bind($src);
			if ($instanceTable->check() === false) {
				return false;
			}
			if (!$instanceTable->store()) {
				return false;
			}
			
			$obj = new StdClass();
			$obj->reservation_instance_id = $instanceTable->id;
			$obj->user_id = $data['owner_id'];
			$obj->user_level = 1; //owner
			
			$this->_db->insertObject('#__jongman_reservation_users', $obj);
		}
		
		$resourceIds = $this->_series->allResourceIds();
		$dbo = $this->getDbo();
		foreach($resourceIds as $i => $resourceId) {
			$resource_level = ($i == 0 ? 1 : 2);
			$obj = new StdClass();
			$obj->reservation_id = (int) $result;
			$obj->resource_id = $resourceId;
			$obj->resource_level = $resource_level;
			
			$success = $dbo->insertObject('#__jongman_reservation_resources', $obj, 'id');
				
		}
		
		
		return true;
	}
	
	/**
	 * Save reservation data to database
	 * @param mixed $data
	 * @return boolean
	 */
	protected function insertSeries($data)
	{
		// Initialise variables;
		$dispatcher = JEventDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		
		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');
		JPluginHelper::importPlugin('extension');
		
		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}
			
			$data['state'] = $data['series']->getStatusId();
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
			if (!($result = $table->store()))
			{
				$this->setError($table->getError());
				return false;
			}
		
			// Clean the cache.
			$this->cleanCache();
		
			// Trigger the onContentAfterSave event.
			$dispatcher->trigger($this->event_after_save, array($this->option . '.' . $this->name, &$table, $isNew));
			$dispatcher->trigger('onReservationSeriesAfterSave', array(&$data, &$table, $result, $isNew));
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
		
			return false;
		}
		
		$pkName = $table->getKeyName();
		
		if (isset($table->$pkName))
		{
			$this->setState($this->option . '.' .$this->getName() . '.id', $table->$pkName);
		}
		$this->setState($this->option . '.' . $this->getName() . '.new', $isNew);

		return $table->$pkName;
	}
	
	public function populateResources($pk = null)
	{
		if (empty($pk)) {
			$pk = (int) $this->getState($this->option . '.' .$this->getName() . '.id');
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__jongman_reservation_resources')
			->where('reservation_id = '.$pk);
		$db->setQuery($query);
		return $db->loadObjectList();	
	}
	
	public function populateInstances($pk = null)
	{
		if (empty($pk)) {
			$pk = (int) $this->getState($this->option . '.' .$this->getName() . '.id');
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);	
		$query->select('*')
				->from('#__jongman_reservation_instances')
				->where('reservation_id = '.$pk);
				
		$db->setQuery($query);
		return $db->loadObjectList();	
	}
	
	public function delete(&$pks) 
	{
		if (parent::delete($pks)) {
			$dbo = $this->getDbo();
			$query = $dbo->getQuery(true);
			
			$query->delete("#__jongman_reservation_resources")
				->where("reservation_id IN (".implode(',', $pks).")");	

			$dbo->setQuery($query);
			$dbo->execute();
			
			$query->clear();
			$query->delete("#__jongman_reservation_users")
				->where("reservation_id IN (".implode(',', $pks).")");
			
			$dbo->setQuery($query);
			$dbo->execute();
			
			return true;
		}
		
		return false;
	}
	
}
