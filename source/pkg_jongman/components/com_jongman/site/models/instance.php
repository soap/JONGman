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
		$table = $this->getTable('Reservation', 'JongmanTable');
		
		$return = $table->loadByInstanceId($pk);	
		// Check for a table object error.
		if ($return === false && $table->getError())
		{
			$this->setError($table->getError());
			return false;
		}
		
		$instance = $table->getReservationInstance();
		$resource_id = $table->getResourceId();
		$properties = $table->getProperties(1);
		$result = JArrayHelper::toObject($properties, 'JObject');
			
		$result->instance_id = $instance->id; 
		$result->resource_id = $resource_id;
		
		$date = new JDate($instance->start_date);
		$result->start_date = $date->format('Y-m-d');
		$result->start_time = $date->format('H:i:s');
		
		$date = new JDate($instance->end_date);
		$result->end_date = $date->format('Y-m-d');
		$result->end_time = $date->format('H:i:s');
		$result->reference_number = $instance->reference_number;
		
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

		if (isset($input['repeat_terminated'])) {
			$terminated = RFDate::parse($input['repeat_terminated'], $tz);
			$terminated->setTime(new RFTime(0, 0, 0, $tz));
		}
		$existingSeries = $this->buildSeries($input);
		
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
		$row = JTable::getInstance('Resource', 'JongmanTable');
		$row->load($input['resource_id']);
		$resource = RFResourceBookable::create($row);
		
		$existingSeries->applyChangesTo($updateScope);
		$existingSeries->update(
				$input['owner_id'], $resource,  $input['title'], $input['description'],
				JFactory::getUser()
			);

		$tz = JongmanHelper::getUserTimezone();
		$input['start_date'] = $input['start_date'].' '.$input['start_time'];
		$input['end_date'] = $input['end_date'].' '.$input['end_time'];
		$duration = new RFDateRange(RFDate::parse($input['start_date'], $tz), RFDate::parse($input['end_date'], $tz));
		$existingSeries->updateDuration($duration);
		$existingSeries->repeats($repeatOption);
		
		//start reservation validation here
		
		//if success then add instances
		$validData['instances'] = $existingSeries->getInstances();

		$validData['repeat_options'] = $repeatOption->configurationString();
		// now we do our validation process
		return $validData;
	}
	
	/**
	 * Method to update existing reservation instance(s)
	 */
	public function update($data, $updateScope='this')
	{
		// Initialise variables;
		$dispatcher = JDispatcher::getInstance();
		$table = $this->getTable();
		$key = $table->getKeyName();
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;
		$instances = $data['instances'];
		var_dump($instances); jexit();
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
	 * Build reservation series from database
	 */
	protected function buildSeries($data) 
	{
		$existingSeries = new RFReservationExistingseries();
		$keys = array('reference_number'=>$data['reference_number']);
		
		$instance = $this->getTable();
		$instance->load($keys);
		
		$reservation = $this->getTable('Reservation', 'JongmanTable');
			
		$reservation->load($instance->reservation_id);
		
		$row = JTable::getInstance('Resource', 'JongmanTable');
		$row->load($reservation->getResourceId());

		$resource = RFResourceBookable::create($row);
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
		$existingSeries->withOwner($reservation->owner_id);
		$existingSeries->withStatus($reservation->state);
		$existingSeries->withPrimaryResource($resource);
		
		$startDate = RFDate::fromDatabase($instance->start_date);
		$endDate = RFDate::fromDatabase($instance->end_date);
		$duration = new RFDateRange($startDate, $endDate);
		$currentInstance = new RFReservation($existingSeries, $duration, $instance->reservation_id, $instance->reference_number);
		$existingSeries->withCurrentInstance($currentInstance);
		
		return $existingSeries;
	}
}