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
	
	private $_instances = array();  // start_date, end_date, reservation_id, reference_number
	
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
		
		if (isset($array['instances'])) {
			$this->_instances = $array['instances'];
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
			$keys = array('reference_number' => $key);
			if ( !$instance->isNew() ) {
				$keys['reservation_id'] = $instance->reservationId();
			} 
			$src = array();
			$src['start_date'] = $instance->startDate()->toDatabase();
			$src['end_date'] = $instance->endDate()->toDatabase();
			$src['reference_number'] = $key;
			$src['reservation_id'] = $this->id;

			$table->load($keys);
			$table->bind($src);	

			$table->store();
		}
		
		return true;
		
	}
	
	function load($keys = null, $reset = true) {
		if ($ret = parent::load($keys, $reset)) {
			$user 	= JFactory::getUser();
			$config = JFactory::getConfig();
			$tz = new DateTimeZone($user->getParam('timezone', $config->get('offset')));
			if ($this->start_date){
				$date = new JDate(date("Y-m-d", $this->start_date), "UTC");
				$date->setTimeZone($tz);
				$this->start_date = $date->format("Y-m-d", true, false);
			}
			
			if ($this->end_date){
				$date = new JDate(date("Y-m-d", $this->end_date), "UTC");
				$date->setTimeZone($tz);
				$this->end_date = $date->format("Y-m-d", true, false);
			}
		}
		return $ret;
	}

	function setDateRange($date)
	{
		
	}
}