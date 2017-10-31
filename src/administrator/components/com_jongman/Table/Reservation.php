<?php
/**

* @package     JONGman Package

*

* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.

* @license     GNU General Public License version 2 or later; see LICENSE.txt

*/

namespace Soap\Component\Jongman\Administrator\Table;
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;

/**
 * Reservation table.
 *
 * @since       1.0
 */
class Reservation extends Table
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabase  $db  A database connector object.
	 *
	 * @return  JongmanTableReservation
	 * @since   1.0
	 */
	public function __construct(\JDatabaseDriver $db)
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
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		
		if (isset($array['attribs']) && is_array($array['attribs'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['attribs']);
			$array['attribs'] = (string) $registry;
		}
		
		return parent::bind($array, $ignore);
	}
	
	
	public function store($updateNulls = false)
	{
		$user = JFactory::getUser();
		$date = JFactory::getDate();
		
		if (empty($this->id)) {
				$this->created_by 	= $user->get('id');
				$this->created 		= $date->toSql();
		}else{
				$this->modified_by 	= $user->get('id');
				$this->modified 	= $date->toSql();
		}		
		return parent::store($updateNulls);
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