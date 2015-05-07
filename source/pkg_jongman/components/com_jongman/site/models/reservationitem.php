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
	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return    void
	 */
	protected function populateState()
	{
		// Load state from the request.
		$pk = JFactory::getApplication()->input->getInt('id');
		$this->setState($this->getName() . '.id', $pk);
	
		// Load the parameters.
		$params = JFactory::getApplication('site')->getParams();
		$this->setState('params', $params);
	
		$access = JongmanHelper::getActions();
		if (!$access->get('core.edit.state') && !$access->get('core.edit')) {
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}
	
	public function getItem($referenceNumber=null)
	{
		if (empty($referenceNumber)) {
			$pk = JFactory::getApplication()->input->getInt('id');
		
			$table = JTable::getInstance('Instance', 'JongmanTable');
			$table->load($pk);
			$referenceNumber = $table->reference_number;
		}

		/* This $pk is an instance id */
		if (!empty($this->_item[$referenceNumber])) return $this->_item[$referenceNumber];

		
		$query = $this->_db->getQuery(true);
		$query->select('a.id, a.start_date, a.end_date, a.reference_number, a.reservation_id')
			->from('#__jongman_reservation_instances AS a');
			
		$query->select('r.title, r.created, r.created_by, r.description, r.owner_id, r.access')
			->join('INNER', '#__jongman_reservations AS r ON r.id=a.reservation_id');
			
		$query->select('own.name as owner_name')
			->join('LEFT', '#__users AS own ON own.id=r.owner_id');
		
		$query->select('ua.name as author')
		->join('LEFT', '#__users AS ua ON ua.id=r.created_by');

		$query->where('a.reference_number='.$this->_db->quote($referenceNumber));
		
		$this->_db->setQuery($query);
		
		$item = $this->_db->loadObject();
		
		$item->users = array();
		$item->params = new JRegistry();
		$item->resources = $this->getResources($item->reservation_id);
		
		// Compute selected asset permissions.
		$user   = JFactory::getUser();
		$uid    = $user->get('id');
		$access = JongmanHelper::getActions($item->reservation_id);
		
		$view_access = true;
		
		if ($item->access && !$user->authorise('core.admin')) {
			$view_access = in_array($item->access, $user->getAuthorisedViewLevels());
		}
		
		$item->params->set('access-view', $view_access);
		
		if (!$view_access) {
			$item->params->set('access-edit', false);
			$item->params->set('access-change', false);
		}
		else {
			// Check general edit permission first.
			if ($access->get('core.edit')) {
				$item->params->set('access-edit', true);
			}
			elseif (!empty($uid) &&  $access->get('core.edit.own')) {
				// Check for a valid user and that they are the owner.
				if ($uid == $item->created_by) {
					$item->params->set('access-edit', true);
				}
			}
		
			// Check edit state permission.
			$item->params->set('access-change', $access->get('core.edit.state'));
		}
		
		$this->_item[$referenceNumber] = $item;
		return $this->_item[$referenceNumber];			
	}
	
	public function getResources($reservationId=null)
	{
		if (empty($reservationId)) {
			$item = $this->getItem();
			$reservationId = $item->reservation_id;	
		}
		
		$query = $this->_db->getQuery(true);
		$query->select('a.resource_id, a.resource_level')
			->from('#__jongman_reservation_resources AS a')
			->select('r.title as resource_title')
			->join('inner', '#__jongman_resources AS r ON r.id=a.resource_id');
			
		$query->where('a.reservation_id='.$reservationId);
		$query->order('a.resource_level ASC');
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
				
	}
	
	/**
	 * 
	 * @return Ambigous <multitype:, string>
	 * @since 3.0
	 */
	public function getTransitions()
	{
		$item = $this->getItem();
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'components/com_workflow/tables');
		$wfInstance = JTable::getInstance('Instance', 'WorkflowTable');
		$wfInstance->load(array('context'=>'com_jongman.reservation', 'item_id'=>$item->reservation_id));
		
		$oUser = JFactory::getUser();
		$transitions = WFApplicationHelper::getTransitionsForInstanceUser($wfInstance, $oUser, true);
		
		return $transitions;
	}
	
	/**
	 * 
	 * @return Ambigous <multitype:, mixed>
	 * @since 3.0
	 */
	public function getLogs()
	{
		$item = $this->getItem();
		return WFApplicationHelper::getTransitionLogs('com_jongman.reservation', $item->reservation_id);
	}
}