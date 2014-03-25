<?php
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
		if (empty($pk)) {
			$pk = JFactory::getApplication()->input->getCmd('id');	
		}
		
		if (!empty($this->_item[$pk])) return $this->_item[$pk];

		$query = $this->_db->getQuery(true);
		
		$query->select('a.id, a.start_date, a.end_date, a.reference_number, a.reservation_id')
			->from('#__jongman_reservation_instances AS a');
						
		$query->select('r.title, r.description, r.schedule_id')
			->join('INNER', '#__jongman_reservations AS r ON r.id=a.reservation_id');
		
		$query->select('u.user_id as owner_id')
			->join('INNER', '#__jongman_reservation_users AS u ON u.reservation_id=a.reservation_id');
			
		$query->select('own.name as owner_name, own.email as owner_email')
			->join('LEFT', '#__users AS own ON own.id=u.user_id');

		$query->where('a.id ='.$this->_db->quote($pk));
		$query->where('u.user_level=1');
		
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
		$query->select('a.id as resource_id, a.title as resource_title')
			->from('#__jongman_reservation_resources AS a');
		
		$query->where('a.reservation_id='.$reservationId);
		
		
	}	
}