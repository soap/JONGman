<?php
/**
 * @version     $Id$
 * @package     JONGman
 * @subpackage  Frontend
 * @copyright   Copyright 2011 Prasit Gebsaap. All rights reserved.
 * @license     GNU General Public License version 2 or later.
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
		
	/**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     *
     * @return    void
     */
    protected function populateState()
    {
        // Load state from the request.
        $pk = JRequest::getInt('id');
        $this->setState($this->getName() . '.id', $pk);

        $return = JRequest::getVar('return', null, 'default', 'base64');
        $this->setState('return_page', base64_decode($return));
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
		$user = JFactory::getUser();
		if ($pk) {
			$table = $this->getTable();
			$return = $table->loadByInstanceId($pk);	
			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($table->getError());
				return false;
			}
			// Convert to the JObject before adding other data.
			
			$instance = $table->getReservationInstance();
			$resource_id = $table->getResourceId();
			$properties = $table->getProperties(1);			
			$result = JArrayHelper::toObject($properties, 'JObject');
			
			$result->instance_id = $instance->id;
			$result->resource_id = $resource_id;
			$result->start_date = $instance->start_date;
			$result->end_date = $instance->end_date;
			$result->reference_number = $instance->reference_number;
		}else{
			$result = parent::getItem($pk);
			
			$result->instance_id = null;
			$result->schedule_id = JRequest::getInt('schedule_id');
			$result->resource_id = JRequest::getInt('resource_id');
			$result->start_date = JRequest::getString('start');
			$result->end_date = JRequest::getString('end');
			$result->owner_id = $user->id;
			$result->created_by = $user->id;	
		}

		$user 	= JFactory::getUser();
		$config = JFactory::getConfig();			

		$tz = $user->getParam('offset');
		$start_date = new RFDate($result->start_date, $tz);
		$end_date = new RFDate($result->end_date, $tz);
		
		$result->start_date = $start_date->format('Y-m-d');
		$result->start_time = $start_date->format('H:i:s');
		$result->end_date = $end_date->format('Y-m-d');
		$result->end_time = $end_date->format('H:i:s');
		
		return $result;
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

	protected function preprocessForm(JForm $form, $data, $group = 'content')
	{
		if (isset($data)) {
			
		}
		parent::preprocessForm($form, $data, $group);

	}
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  The table object for the record.
	 *
	 * @return  boolean  True if successful, otherwise false and the error is set.
	 * @since   1.0
	 */
	protected function prepareTable($table)
	{	
		return true;
	}
	
	
	/**
	 * Method to save the new reservation data.
	 * @param   array  $data  The form data.
	 * @return  boolean  True on success, False on error.
	 */
	public function save($data)
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		// Include the content plugins for the on save events.
		JPluginHelper::importPlugin('content');

		// Allow an exception to be thrown.
		try
		{
			// Load the row if saving an existing record.
			if ($pk > 0)
			{
				$table->load($pk);
				$isNew = false;
			}

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
		}
		catch (Exception $e)
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

		return true;
	}	
	
	/**
	 * Method to update existing reservation instance(s)
	 */
	public function update($data, $updateScope='this')
	{
		
	}
	
	/**
	 * override to add resource reservation validation 
	 * @see JModelForm::validate()
	 */
	public function validate($form, $data, $group = null)
	{
		$validData = parent::validate($form, $data, $group);
		if ($validData === false) return false;
		
		$validData['start_date'] = $validData['start_date'].' '.$validData['start_time'];
		$validData['end_date'] = $validData['end_date'].' '.$validData['end_time'];
		
		$input = $validData;
		$tz = JongmanHelper::getUserTimezone();

		if (isset($input['repeat_terminated'])) {
			$terminated = RFDate::parse($input['repeat_terminated'], $tz);
			$terminated->setTime(new RFTime(0, 0, 0, $tz));
		}
		switch ((string) $input['repeat_type']) {
			case 'daily': 
					$repeatOption = new RFReservationRepeatDaily(
												$input['repeat_interval'], $terminated);
				break;
			case 'weekly' :
					$repeatOption = new RFReservationRepeatWeekly(
												$input['repeat_interval'], $terminated,
												$input['repeat_days']
											);
				break;
			case 'monthly' :
					$class = 'RFReservationRepeat'.ucfirst($input['repeat_monthly_type']);
					$repeatOption = new $class(
												$input['repeat_interval'], $terminated				
											);
				break;
			case 'yearly' :
					$repeatOption = new RFReservationRepeatYearly(
												$input['repeat_interval'], $terminated					
										);
				break;
			default:
					$repeatOption = new RFReservationRepeatNone();
				break;
				
		}

		$input['repeatOptions'] = $repeatOption;
		$reservationSeries = new RFReservationSeries();
		$reservationSeries->bind($input);
		//start reservation validation here
		
		//if success then add instances
		$validData['instances'] = $reservationSeries->getInstances();

		$validData['repeat_options'] = $repeatOption->configurationString();
		// now we do our validation process
		return $validData;
	}
	
}
