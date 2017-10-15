<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
/**
 * The Jongman Reservation Item model
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       1.0
 */
class JongmanModelInstanceitem extends JModelItem 
{
	
	public function getItem($pk=null)
	{
		$mode = JFactory::getApplication()->input->getCmd('mode');
		if ($mode='popup') {
			return $this->getItemByRef();
		}	
		
		if (empty($pk)) {
			$pk = JFactory::getApplication()->input->getCmd('id');	
		}
		
		if (!empty($this->_item[$pk])) return $this->_item[$pk];

		$query = $this->_db->getQuery(true);
		
		$query->select('a.id, a.start_date, a.end_date, a.reference_number, a.reservation_id')
			->from('#__jongman_reservation_instances AS a');
						
		$query->select('r.title, r.description, r.schedule_id')
			->join('INNER', '#__jongman_reservations AS r ON r.id=a.reservation_id');
			
		$query->select('own.name as owner_name, own.email as owner_email')
			->join('LEFT', '#__users AS own ON own.id=r.owner_id');

		$query->where('a.id ='.$this->_db->quote($pk));
		
		$this->_db->setQuery($query);
		
		$this->_item[$pk] = $this->_db->loadObject();

		return $this->_item[$pk];			
	}
	
	public function getItemByRef($pk = null)
	{
		if (empty($pk)) {
			$pk = JFactory::getApplication()->input->getCmd('id');
		}
		
		if (!empty($this->_item[$pk])) return $this->_item[$pk];
		
		$query = $this->_db->getQuery(true);
		
		$query->select('a.id, a.start_date, a.end_date, a.reference_number, a.reservation_id')
		->from('#__jongman_reservation_instances AS a');
		
		$query->select('r.title, r.description, r.schedule_id')
		->join('INNER', '#__jongman_reservations AS r ON r.id=a.reservation_id');
			
		$query->select('own.name as owner_name, own.email as owner_email')
		->join('LEFT', '#__users AS own ON own.id=r.owner_id');
		
		$query->where('a.reference_number ='.$this->_db->quote($pk));
		
		$this->_db->setQuery($query);
		
		$this->_item[$pk] = $this->_db->loadObject();
		
		return $this->_item[$pk];		
	}
	
	public function getResources($reservationId=null)
	{
		if (empty($reservationId)) {
			$item = $this->getItem();
			$reservationId = $item->reservation_id;	
		}
		
		$query = $this->_db->getQuery(true);
		$query->select('r.*, a.resource_level as resource_level')
			->from('#__jongman_reservation_resources AS a')
			->join('LEFT', '#__jongman_resources AS r ON r.id=a.resource_id');
		
		$query->where('a.reservation_id='.(int)$reservationId);
		$query->order('a.resource_level ASC, r.title ASC');
		
		$this->_db->setQuery($query);
		$rows = $this->_db->loadObjectList();
		
		return $rows;
	}	
}