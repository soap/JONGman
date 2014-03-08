<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modelitem');
/**
 * The Jongman ResourceForm model extends from backend Resource model.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       1.0
 */
class JongmanModelReservationitem extends JModelItem
{
	
	public function getItem($referenceNumber=null)
	{
		if (empty($referenceNumber)) {
			$pk = JFactory::getApplication()->input->getCmd('id');	
		}
		echo $pk; exit();
		if (!empty($this->_item[$referenceNumber])) return $this->_item[$referenceNumber];

		
		$query = $this->_db->getQuery(true);
		$query->select('a.start_date, a.end_date, a.reference_number, a.reservation_id')
			->from('#__jongman_reservation_instances AS a');
			
		$query->select('r.title, r.description, r.owner_id')
			->join('INNER', '#__jongman_reservations AS r ON r.id=a.reservation_id');
			
		$query->select('own.name as owner_name')
			->join('LEFT', '#__users AS own ON own.id=r.owner_id');

		$query->where('a.reference_number='.$this->_db->quote($referenceNumber));
		
		$this->_db->setQuery($query);
		
		$this->_item[$referenceNumber] = $this->_db->loadObject();
		
		return $this->_item[$referenceNumber];			
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