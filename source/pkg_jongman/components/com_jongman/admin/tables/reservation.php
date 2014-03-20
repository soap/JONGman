<?php
/**
 * @version     $Id$
 * @package     
 * @subpackage  
 * @copyright   Copyright 2011 New Life in IT Pty Ltd. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;


jimport('joomla.database.table');

/**
 * Reservation table.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       1.0
 */
class JongmanTableReservation extends JTable
{
	/**
	 * Each instance contains start_date, end_date, reservation_id, reference_number
	 */
	private $_instances = array();  

	private $_current_instance_id = null; // current instance id

	/**
	 * array of all reserved resource id
	 */
	private $_resources = array();
	
	private $_resource_id = null;
	private $_resource_ids = array();
	/**
	 * Constructor.
	 *
	 * @param   JDatabase  $db  A database connector object.
	 *
	 * @return  JongmanTableReservation
	 * @since   1.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__jongman_reservations', 'id', $db);
	}

	/**
	 * Override check function to validate reservation data
	 * as well as availability of resource being reserved
	 * @see JTable::check()
	 */
	public function check() 
	{
		return true;	
	}
	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array   $array   The input array to bind.
	 * @param   string  $ignore  A list of fields to ignore in the binding.
	 *
	 * @return  null|string	null is operation was satisfactory, otherwise returns an error
	 * @see     JTable:bind
	 * @since   1.0
	 */
	public function bind($array, $ignore = '')
	{
		$success = parent::bind($array, $ignore);
		if (!$success) return false;

		$user = JFactory::getUser();
		$date = JFactory::getDate();
	
		if (empty($this->_tbl_key)) {
			if (isset($this->created_by) && empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}	
		}else{
			if (isset($this->modified_by)) {
				$this->modifed_by = $user->get('id');
			}
			if (isset($this->modified)) {
				$this->modified = $date->toSql();
			}
		} 
		
		if (isset($array['series'])) {
			$this->_instances = $array['series']->getInstances();
			$this->_resource_ids = $array['series']->allResourceIds();
		}
		

		return true;
	}

	/**
	 * Overload the store method for the Weblinks table.
	 *
	 * @param   boolean  $updateNulls  Toggle whether null values should be updated.
	 *
	 * @return  boolean  True on success, false on failure.
	 * @since   1.0
	 */
	public function store($updateNulls = false)
	{	
		$stored =  parent::store($updateNulls);
		if (!$stored) return false;
		
		if (empty($this->_instances)) return stored;
		$table = JTable::getInstance('Instance', 'JongmanTable');
		
		foreach($this->_instances as $key => $instance ) {
			
			$src = array();		

			$src['start_date'] = $instance->startDate()->toDatabase();
			$src['end_date'] = $instance->endDate()->toDatabase();
			$src['reference_number'] = $key;
			$src['reservation_id'] = $this->id;

			$table->reset();
			$table->id = null;
			$table->bind($src);	
				
			if (!$table->store()) {
				JFactory::getApplication()->enqueueMessage('Error insert instance, '.$key);
				continue;
			}
				
		}
		if (!empty($this->_resource_ids)) {
			foreach($this->_resource_ids as $x => $rid) {
				$obj = new StdClass();
				$obj->reservation_id = $this->id;
				$obj->resource_id = $rid;
				$obj->resource_level = ($x == 0 ? 0 : 1);
				$this->_db->insertObject('#__jongman_reservation_resources', $obj);
			}		
		}else{
			JFactory::getApplication()->enqueueMessage('Resource ID empty', 'warning');	
		}	
		return true;
		
	}
	
	function load($keys = null, $reset = true) {
		$result = parent::load($keys, $reset);
		if ($result == false) return false;
		
		$this->loadReservationInstances();
		//$this->loadReservationResources();
		return $result;
	}
	
	public function loadByInstanceId($instanceId) 
	{
		if (empty($instanceId)) return false;
		$instance = JTable::getInstance('Instance', 'JongmanTable');
		$instance->load($instanceId);

		if ((int)$instance->reservation_id > 0) {
			$result = $this->load($instance->reservation_id);
			$this->_current_instance_id = $instance->id;
			
			return $result;
		}
		
		return false;			
	}
	
	public function getReservationInstance($instanceId=null)
	{
		if (empty($instanceId)) $instanceId = $this->_current_instance_id;
	
		return $this->_instances[$instanceId];	
	}
	
	public function getResourceId() 
	{
		return $this->_resource_id;	
	}
	
	private function loadReservationInstances()
	{
		$query = $this->_db->getQuery(true);
		$query->select('*')->from('#__jongman_reservation_instances')
			->where('reservation_id ='.(int)$this->id);
		$this->_db->setQuery($query);
		$this->_instances = $this->_db->loadObjectList('id', 'JObject');
		
		return true;		
	}
	
	private function loadReservationResources()
	{
		if (empty($this->_tbl_key)) {
			JFactory::getApplication()->enqueueMessage('Reservation primary key is empty, cannot load resource', 'warning');
			return;
		}
		$query = $this->_db->getQuery(true);
		$query->select('resource_id')
			->from('#__jongman_reservation_resources')
			->where('reservation_id='.(int)$this->$this->_tbl_key);
		$this->_db->setQuery($query);
		
		$this->_resource_ids = $this->_db->loadColumn();

		$this->_resource_id = $this->_resource_ids[0];	
	}
	
}