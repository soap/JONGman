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
		$user = JFactory::getUser();
		$date = JFactory::getDate();
	
		if (empty($this->id)) {
			if (isset($this->created_by) && empty($this->created_by)) {
				$this->created_by = $user->get('id');
			}	
		}else{
			if (isset($this->modified_by)) {
				$this->modified_by = $user->get('id');
			}
			if (isset($this->modified)) {
				$this->modified = $date->toSql();
			}
		} 
		
		return parent::bind($array, $ignore);;
	}
	
	public function loadByInstanceId($instanceId) 
	{
		if (empty($instanceId)) return false;
		$instance = JTable::getInstance('Instance', 'JongmanTable');
		$instance->load($instanceId);

		if ((int)$instance->reservation_id > 0) {
			$result = $this->load($instance->reservation_id);
			
			return $result;
		}
		
		return false;			
	}
}