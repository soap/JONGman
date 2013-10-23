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
 * @subpackage  Frontend
 * @since       1.0
 */
class JongmanTableReservation extends JTable
{
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
		return  parent::bind($array, $ignore);
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
		// Initialiase variables.
		$date	= JFactory::getDate()->toSql();
		$user 	= JFactory::getUser();
		$config = JFactory::getConfig();
		
		if (empty($this->id)) {
			// New record.
			$this->created_time		= $date;
			$this->created_by		= $user->get('id');
		} 
		else {
			// Existing record.
			$this->modified_time	= $date;
			$this->modified_by		= $user->get('id');
		}
		
		if (empty($this->reserved_for)) {
			$this->reserved_for = $user->get('id');
		}
		
		$tz = new DateTimeZone($user->getParam('timezone', $config->get('offset')));
		$date = JDate::getInstance($this->start_date, $tz);
		
		$this->start_date = $date->toUnix();
		
		$dbo = $this->getDbo();
		$query = $dbo->getQuery(true);
		$query->select('allow_multi');
		$query->from('#__jongman_resources');
		$query->where('id = '.$this->resource_id);
		
		$dbo->setQuery($query);
		if ($dbo->loadResult()==1) {
			$date = JDate::getInstance($this->end_date, $tz);
			$this->end_date = $date->toUnix();
		}else{
			$this->end_date = $this->start_date;
		}
		
		if ($this->end_date < $this->start_date) {
			$this->end_date = $this->start_date;
		}
		
		// Attempt to store the data.
		return parent::store($updateNulls);
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
	
}