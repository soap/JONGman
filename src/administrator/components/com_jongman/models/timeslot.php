<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Timeslot model.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       1.0
 */
class JongmanModelTimeslot extends JModelAdmin
{
	/**
	 * Override pk retrieval
	 * @see JModelAdmin::populateState()
	 */
	protected function populateState()
	{
		// Get the pk of the record from the request.
		$pk = JFactory::getApplication()->input->getInt('layout_id');
		$this->setState($this->getName() . '.id', $pk);
	
		// Load the parameters.
		$value = JComponentHelper::getParams($this->option);
		$this->setState('params', $value);
	}	
	/**
	 * Method to get the Timeslot form.
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
	 * Method to get a Timeslot.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *					 Here we refer to layout_id
	 * @return  mixed    Category data object on success, false on failure.
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$layout = $this->getLayout($pk);
		
		$result = new StdClass();
		$result->layout = $layout;
		$result->layout_id = $pk;
		$result->dailylayout = 1;
		$result->blocked_slot ='';
		$result->reservable_slots = '';
		$result->timezone = $layout->timezone;
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('day_of_week, label, end_label, start_time, end_time, availability_code')
			->from('#__jongman_time_blocks')
			->where('layout_id = '.$db->quote($pk))
			->order('start_time ASC, end_time ASC');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		
		if ($rows == false || $rows == null){
			return $result;	
		}
		
		$reservableSlots = array();
		$blockedSlots = array();
		$weekDayReservableSlots = array();
		$weekDayBlockedSlots = array();
		if ($rows = $db->loadObjectList()) {
			foreach ($rows as $row) {
				$start = $row->start_time;
				$end = $row->end_time;
				$row->start_time = RFTime::parse($start, $layout->timezone)->format('H:i');
				$row->end_time = RFTime::parse($end, $layout->timezone)->format('H:i');
				if (!empty($row->day_of_week)) {
					if ($row->availability_code == '1') {
						$weekDayReservableSlots[$row->day_of_week][] = $row->start_time.' - '.$row->end_time;
					}else{
						$weekDayBlockedSlots[$row->day_of_week][]= $row->start_time.' - '.$row->end_time;
				
					}		
				}else{
					if ($row->availability_code == '1'){
						$reservableSlots[] = $row->start_time.' - '.$row->end_time; 	
					}else{
						$blockedSlots[] = $row->start_time.' - '.$row->end_time;
					}
				} 	
			}
			$result->reservable_slots = implode("\n", $reservableSlots);
			$result->blocked_slots = implode("\n", $blockedSlots);
			if (count($weekDayReservableSlots)) {
				foreach($weekDayReservableSlots as $day => $slots) {
					$field = 'day'.$day.'_reservable_slots';
					$result->$field = implode("\n", $slots);	
				}	
			}
			
			if (count($weekDayBlockedSlots)) {
				foreach($weekDayBlockedSlots as $day => $slots) {
					$field = 'day'.$day.'_blocked_slots';
					$result->$field = implode("\n", $slots);
				}	
			}	
		}else{
			return $result;
		} 
		
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
	public function getTable($type = 'Timeslot', $prefix = 'JongmanTable', $config = array())
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
	 * Prepare the form before display to the user.
	 *
	 * @param   JForm  $form  The table object for the record.
	 * @param   	   $data  The data binded to $form
	 * @param	string $group group of plugin to trigger
	 * @return  boolean  True if successful, otherwise false and the error is set.
	 * @since   1.0
	 */	 
	 
	protected function preprocessForm(JForm $form, $data, $group='content')
	{	
		parent::preprocessForm($form, $data, $group);
	}
	
	/**
	 * Validate data
	 * @see JModelForm::validate()
	 */
	public function validate($form, $data, $group = null)
	{
		$validData = parent::validate($form, $data, $group);
		if ($validData == false) return false;

		$valid = true;
		$days = array(null);
		
		$validateSingle = isset($validData['dailylayout']);

		if (!$validateSingle){
			if (count($reservableSlots) != RFDayOfWeek::numberOfDays || count($blockedSlots) != RFDayOfWeek::numberOfDays) {
				return false;
			}
			$layout = RFLayoutSchedule::parseDaily('UTC', $reservableSlots, $blockedSlots);
			$days = RFDayOfWeek::Days();
		}
		else{
			$reservableSlots = $data['reservable_slots'];
			$blockedSlots = $data['blocked_slots'];
			$layout = RFLayoutSchedule::parse('UTC', $reservableSlots, $blockedSlots);
		}

		foreach ($days as $day) {
			if (is_null($day)) {
				$day = 0;
			}
			$slots = $layout->getLayout(RFDate::now()->addDays($day)->toUtc());

			/** @var $firstDate Date */
			$firstDate = $slots[0]->beginDate();
			/** @var $lastDate Date */
			$lastDate = $slots[count($slots) - 1]->endDate();
			if (!$firstDate->isMidnight() || !$lastDate->isMidnight()) {
				//Log::Debug('Dates are not midnight');
				$this->setError("COM_JONGMAN_ERROR_SLOTS_START_FROM_MIDNIGHT_TO_MIDNIGHT");
				$valid = false;
				return false;
			}

			for ($i = 0; $i < count($slots) - 1; $i++) {
				if (!$slots[$i]->endDate()->equals($slots[$i + 1]->beginDate())) {
					$this->setError("COM_JONGMAN_ERROR_SLOTS_FILL_ALL_DAY");
					$valid = false;
					return false;
				}
			}
		}
		
		if ($valid === false) return false;
		
		return $validData;
	}
	/**
	 * (non-PHPdoc)
	 * @see JModelAdmin::save()
	 */
	public function save($data)
	{
		// Initialise variables;
		$key = 'layout_id';
		$pk = (!empty($data[$key])) ? $data[$key] : (int) $this->getState($this->getName() . '.id');
		$isNew = true;

		$db = $this->getDbo();
		// Allow an exception to be thrown.
		try
		{
			$data['single_layout'] = '1';
			$timezone = $data['timezone'];
			if ($data['single_layout'] == '1') {
				$reservableSlots = $data['reservable_slots'];
				$blockedSlots = $data['blocked_slots'];
				$layout = RFLayoutSchedule::parse($timezone, $reservableSlots, $blockedSlots);
				$slots = $layout->getSlots();

				$query = $db->getQuery(true);
				$query->delete()->from('#__jongman_time_blocks')
					->where('layout_id ='.(int)$pk);
				$db->setQuery($query);
				$db->execute();
				
				foreach ($slots as $slot) {
					$obj = new StdClass();
					$obj->start_time 		= $slot->start->format('H:i:s');
					$obj->end_time 			= $slot->end->format('H:i:s');
					$obj->availability_code = $slot->periodType;
					$obj->layout_id 		= $pk;
					$db->insertObject('#__jongman_time_blocks', $obj, 'id'); 
				}	
			}else{
				
			}
			
			
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
	
			return false;
		}
	
		return true;
	}
	
	/**
	 * get Layout object from database
	 */
	protected function getLayout($pk)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')->from('#__jongman_layouts')->where('id='.(int)$pk);
		
		$db->setQuery($query);
		return $db->loadObject();		
	}
	
	public function getTimeSlots($layoutId) 
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('availability_code, start_time')
			->from('#__jongman_time_blocks')->where('layout_id ='.(int)$layoutId)
			->order('start_time ASC');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		foreach ($rows as $i => $row) {
			$label = JDate::getInstance($row->start_time, 'UTC')->format('H:i');
			$rows[$i]->label = $label; 
		}
		
		return $rows;
	}
}
