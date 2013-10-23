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
		// add field definitions from backend
		JForm::addFieldPath(JPATH_COMPONENT_ADMINISTRATOR . '/models/fields');
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
		if ($result = parent::getItem($pk)) {
			if (empty($pk)){
				$result->schedule_id = JRequest::getInt('schedule_id');
				$result->resource_id = JRequest::getInt('resource_id');
				
				$user 	= JFactory::getUser();
				$config = JFactory::getConfig();
				
				$ts = JRequest::getInt('ts');
				
				// set date time to UTC ($tz = null)
				$tz = null;
				$result->start_date = JDate::getInstance(date("Y-m-d H:i:s",$ts), $tz)->format("Y-m-d");
				$result->end_date = $result->start_date;
				
				$result->start_time = JRequest::getInt('tstart');
				$result->end_time = JRequest::getInt('tend');
				$result->reserved_for = $user->id;
			} 			
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
			if (is_array($data)) $data = JArrayHelper::toObject($data);
			if (is_object($data)) {
				if (isset($data->schedule_id) && $data->schedule_id) {
					$form->setFieldAttribute('resource_id', 'schedule_id', $data->schedule_id);
				
					$form->setFieldAttribute('start_time', 'schedule_id', $data->schedule_id);
					$form->setFieldAttribute('end_time', 'schedule_id', $data->schedule_id);
				}
				if (isset($data->resource_id) && $data->resource_id) {
					$resource = JTable::getInstance('Resource', 'JongmanTable');
					$resource->load((int)$data->resource_id);
					$form->setFieldAttribute('end_date', 'readonly', ($resource->allow_multi?'false':'true'));
					if (!$resource->allow_multi) {
						$form->setFieldAttribute('end_date', 'type', 'text');
					}	
				}
			
				$proxyReservation = (bool)JComponentHelper::getParams('com_jongman')->get('proxyReservation');
				if (isset($data->reserved_for) && !$proxyReservation) {
					$form->setFieldAttribute('reserved_for', 'readonly', 'true');
				}
				
				if (!empty($data->id)) {
					$form->setFieldAttribute('repeat_interval', 'readonly', 'true');
				}
			}

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

		jimport('joomla.filter.output');

		// Prepare the alias.
		$table->alias = JApplication::stringURLSafe($table->alias);
		$params = JComponentHelper::getParams('com_jongman');
		$referLength = (int)$params->get('referLength');
		
		if ($referLength <= 6) $referLength = 6;
		// If the alias is empty, prepare from the value of the title.
		if (empty($table->alias) || strlen($table->alias)) {
			$table->alias = JUserHelper::genRandomPassword($referLength);
		}
		
		return true;
	}
	
	public function getSchedule($schedule_id = null) 
	{
		if (empty($this->_schedules)) $this->_schedules = array();
		
		if (!empty($this->_schedules[$schedule_id])) {
			return $this->_schedules[$schedule_id];
		}
		
		if (empty($schedule_id)) {
			$schedule_id = JRequest::getInt('schedule_id');	
		}
		if (empty($schedule_id)) return null;
		
		$result = JTable::getInstance('Schedule', 'JongmanTable');
		$result->load($schedule_id);
		
		$this->_schedules[$schedule_id] = $result;
		return $this->_schedules[$schedule_id];
	}
	
	public function getResource($resource_id = null)
	{
		if (empty($this->_resources)) $this->_resources = array();
		
		if (!empty($this->_resources[$resource_id])) {
			return $this->_resources[$resource_id];
		}
		if (empty($resource_id)) {
			$resource_id = JRequest::getInt('resource_id');	
		}
		if (empty($resource_id)) return null;
		
		$result = JTable::getInstance('Resource', 'JongmanTable');
		$result->load($resource_id);
		
		$this->_resources[$resource_id] = $result;
		return $this->_resources[$resource_id];	
	}
	/**
	 * 
	 * Validate resource availability
	 * @param unknown_type $data
	 */
	public function validateResource($data = null) 
	{
		$result = (!$this->isBooked($data));

		return $result;
	}
	
	/**
	 * 
	 * Check if reservation date is passed, allow only for Admin
	 * @param unknown_type $data
	 */
	protected function checkStartDate($data)
	{
		$user = JFactory::getUser();
		if (is_array($data)) $data = JArrayHelper::toObject($data);
		if ($user->authorise('core.admin', 'com_jongman.resource.'.$data->resource_id)) 
		{ 
			return true;
		}
		
		$date = JFactory::getDate();
		
		return true;
	}
	
	/**
	 * 
	 * Check if the resource was booked or not
	 * @param unknown_type $data
	 */
	
	protected function isBooked($data) 
	{
		$user 	= JFactory::getUser();
		$config = JFactory::getConfig();
		
		$rs = JArrayHelper::toObject($data);
		$tz = new DateTimeZone($user->getParam('timezone', $config->get('offset')));
		$start_date = JDate::getInstance($rs->start_date, $tz);
		$rs->start_date = $start_date->toUnix();
		
		$end_date = JDate::getInstance($rs->end_date, $tz);
		$rs->end_date = $end_date->toUnix();
		
 		// If it starts between the 2 dates, ends between the 2 dates, or surrounds the 2 dates, get it
		$query = "SELECT COUNT(id) AS num FROM #__jongman_reservations "
				. " WHERE resource_id = {$rs->resource_id} "
				. " AND ("
					// Is surrounded by
					//(starts on a later day OR starts on same day at a later time) AND (ends on an earlier day OR ends on the same day at an earlier time)					
					. " ( (start_date > {$rs->start_date} OR (start_date = {$rs->start_date} AND start_time > {$rs->start_time})) AND ( end_date < {$rs->end_date} OR (end_date = {$rs->end_date} AND end_time < {$rs->end_time})) )"
					// Surrounds
					//(starts on an earlier day OR starts on the same day at an earlier time) AND (ends on a later day OR ends on the same day at a later time)
					. " OR ( (start_date < {$rs->start_date}  OR (start_date = {$rs->start_date}  AND start_time < {$rs->start_time})) AND (end_date > {$rs->end_date}  OR (end_date = {$rs->end_date}  AND end_time > {$rs->end_time})) )"
					// Conflicts with the starting time
					//(starts on an earlier day OR starts on the same day at an earlier time) AND (ends on a later day than the starting day OR ends on the same day as the starting day but at a later time)
					. " OR ( (start_date < {$rs->start_date} OR (start_date = {$rs->start_date} AND start_time <= {$rs->start_time} )) AND (end_date > {$rs->start_date} OR (end_date = {$rs->start_date} AND end_time > {$rs->start_time} )) ) "
					// Conflicts with the ending time
					//(starts on an earlier day than this ends OR starts on the same day as this ends but at an earlier time) AND (ends on a day later than the ending day OR ends on the same day as the ending day but at a later time) 
					. " OR ( (start_date < {$rs->end_date} OR (start_date = {$rs->end_date}  AND start_time < {$rs->end_time})) AND (end_date > {$rs->end_date}  OR (end_date = {$rs->end_date} AND end_time >= {$rs->end_time} )) )"
				. " ) "; 
        if (!empty($rs->id)) $query .= " AND id <> ".$rs->id;
        $dbo = $this->getDbo();
        $dbo->setQuery($query);
		$sql = $dbo->getQuery();
		
		$count = (int)$dbo->loadResult();
        return ($count > 0);   		
	}
}
