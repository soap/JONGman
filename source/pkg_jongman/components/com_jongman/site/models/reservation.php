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
jimport('jongman.date.date');
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
		$result = parent::getItem($pk);
		$user 	= JFactory::getUser();
		$config = JFactory::getConfig();
		
		if (empty($pk)){
			$result->schedule_id = JRequest::getInt('schedule_id');
			$result->resource_id = JRequest::getInt('resource_id');
			$result->start_date = JRequest::getString('start');
			$result->end_date = JRequest::getString('end');
			$result->reserved_for = $user->id;
		}else{
			
		} 			

		$tz = $user->getParam('offset');
		$start_date = new JMDate($result->start_date, $tz);
		$end_date = new JMDate($result->end_date, $tz);
		
		$result->start_date = $start_date->format('Y-m-d');
		$result->end_date = $end_date->format('Y-m-d');
		
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
	 * Check if the resource was booked or not
	 * @param unknown_type $data
	 */
	
	protected function isBooked($data) 
	{

	}
}
