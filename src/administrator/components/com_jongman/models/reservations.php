<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
jimport('joomla.application.component.modellist');

/**
 * reservation model.
 *
 * @package     JONGman
 * @subpackage  Administrator
 * @since       1.0
 */
class JongmanModelReservations extends JModelList
{
	/**
	 * Constructor override.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @return  JongmanModelReservations
	 * @since   1.0
	 * @see     JModelList
	 */

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id', 'instance_id',
				'title', 'r.title', 
				'resource_title', 'schedule_title',
				'r.owner_id', 'owner',
				'reference_numnber', 'a.reference_number',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'a.start_date', 'a.end_date',
				'state', 'r.state',
				'access', 'r.access', 'access_level',
				'created', 'r.created',
				'created_by', 'r.created_by', 'author',
				'modified', 'r.modified',
				'modified_by', 'r.modified_by',
			);
		}
		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 * @since   1.0
	 */
	protected function populateState($ordering = 'r.title', $direction = 'asc')
	{
		// Set list state ordering defaults.
		parent::populateState($ordering, $direction);
		
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '', 'int');
		$this->setState('filter.state', $published);

		$scheduleId = $this->getUserStateFromRequest($this->context.'.filter.schedule_id', 'filter_schedule_id', null, 'int');
		$this->setState('filter.schedule_id', $scheduleId);
		
		$resourceId = $this->getUserStateFromRequest($this->context.'.filter.resource_id', 'filter_resource_id', null, 'int');
		$this->setState('filter.resource_id', $resourceId);
		
		$reservationType = $this->getUserStateFromRequest($this->context.'.filter.reservation_type', 'filter_reservation_type', null, 'int');
		$this->setState('filter.reservation_type', $reservationType);
		
		$startDate = $this->getUserStateFromRequest($this->context.'.filter.start_date', 'filter_start_date', null);
		$this->setState('filter.start_date', $startDate);
		
		$end_date = $app->getUserStateFromRequest($this->context.'.filter.end_date', 'filter_end_date', null);
		if (!empty($start_date)) {
			$tz = RFApplicationHelper::getUserTimezone();
			$startDate = new RFDate($start_date, $tz);
			if (empty($end_date)) $end_date = $start_date;
			$endDate = new RFDate($end_date, $tz);
			if ($endDate->lessThan($startDate) || $endDate->equals($startDate)) { 
				$endDate = $startDate;
				$end_date = $endDate->addDays(1)->format('Y-m-d');
				$app->setUserState($this->context.'.filter.end_date', $end_date);
			}
		}
		
		$workflow_state_id = $app->getUserStateFromRequest($this->context.'.filter.workflow_state_id', 'filter_workflow_state_id', null, 'int');
		$this->setState('filter.workflow_state_id', $workflow_state_id);
		
		$repeatType = $this->getUserStateFromRequest($this->context.'.filter.repeat_type', 'filter_repeat_type', null);
		$this->setState('filter.repeat_type', $repeatType);
		// Load the parameters.
		$params = JComponentHelper::getParams('com_jongman');
		$this->setState('params', $params);		
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   1.0
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true); 

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 
				'a.id as instance_id, a.start_date, a.end_date, a.reference_number, a.reservation_id')
		);
		$query->from('#__jongman_reservation_instances AS a');
		
		$query->select('r.alias as alias, r.title as title, ' .
			'r.checked_out, r.checked_out_time, ' .
			'r.state, r.access, r.created, r.schedule_id, r.id as reservation_id');
		
		$query->join('INNER','#__jongman_reservations AS r ON r.id=a.reservation_id');
		
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=r.checked_out');
		
		$query->select('o.user_id as owner_id, o.user_level');
		$query->join('LEFT', '#__jongman_reservation_users AS o ON o.reservation_instance_id=a.id');
		
		// Join over the users for the owner user.
		$query->select('own.name AS owner');
		$query->join('LEFT', '#__users AS own ON own.id=o.user_id');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id=r.access');

		// Join over the schedules.
		$query->select('sc.name AS schedule_title');
		$query->join('LEFT', '#__jongman_schedules AS sc ON sc.id=r.schedule_id');
		
		$query->select('rr.resource_id as resource_id');
		$query->join('LEFT', '#__jongman_reservation_resources AS rr ON r.id=rr.reservation_id AND resource_level=1');	
		
		$query->select('rs.title as resource_title');
		$query->join('LEFT', '#__jongman_resources AS rs ON rs.id=rr.resource_id');
				
		// Join over the users for the author.
		$query->select('au.name AS author');
		$query->join('LEFT', '#__users AS au ON au.id=r.created_by');
		

		if ($reservationType = $this->getState('filter.reservation_type')) {
			$query->where('r.type_id ='.(int)$reservationType);
		}
		
		if ($scheduleId = $this->getState('filter.schedule_id')) {
			$query->where('r.schedule_id='.(int)$scheduleId);	
		}
		
		$userLevel = $this->getState('filter.user_level', '1');
		$query->where('o.user_level='.(int)$userLevel);
		
		if ($access = $this->getState('filter.access')) {
			$query->where('r.access = '.$access);
		}
		
		if ($resourceId = $this->getState('filter.resource_id')) {
			$query->where('a.reservation_id IN 
				(SELECT reservation_id FROM #__jongman_reservation_resources WHERE resource_id='.(int)$resourceId.')');
		}

		$repeatType = $this->getState('filter.repeat_type');
		if (!empty($repeatType)) {
			$query->where('r.repeat_type='.$db->quote($repeatType));
		}
		
		$timezone = RFApplicationHelper::getUserTimezone();
		$startDate = $this->getState('filter.start_date');
		if (!empty($startDate)) {
			$start_date = JDate::getInstance($startDate, $timezone)->toSql();
			$query->where('a.start_date >='.$db->quote($start_date));	
		}
		
		$endDate = $this->getState('filter.end_date');
		if (!empty($endDate)) {
			$end_date = JDate::getInstance($endDate, $timezone)->toSql();
			$query->where('a.end_date <='.$db->quote($end_date));				
		}
		
		$workflowStateId = $this->getState('filter.workflow_state_id');
		
		if ($workflowStateId) {
			$itemIds = $this->getItemIdsByWorkflowState($workflowStateId);
			if (empty($itemIds)) {
				$query->where('r.id = -1');	// no record so we simulate it
			}else{
				$query->where('r.id IN ('.implode(',', $itemIds).')');
			}
		}else{
			// Filter by published state
			$published = $this->getState('filter.state');
			if (is_numeric($published)) {
				$query->where('r.state = ' . (int) $published);
			} else if ($published === '') {
				$query->where('(r.state = -1 OR r.state = 0 OR r.state = 1)');
			}
		}
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.reference_number');
		$orderDirn	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol.' '.$orderDirn));

		return $query;
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		if ($items === false) return false;
		$user = JFactory::getUser();
		$timezone = RFApplicationHelper::getUserTimezone();
		
		$params = $this->getState('params');
		if ($params === null) {
			// $params === null as this class get called with ignore_request = true
			$params = JComponentHelper::getParams('com_jongman');
			$this->setState('params', $params);
		}
			
		$workflow = (bool)($this->getState('params')->get('approvalSystem') == 2);
		if ($workflow) {
			jimport('workflow.framework');
		}
		$dbo = $this->getDbo();
		$query = $dbo->getQuery(true);
		foreach($items as $i => $item) {
			$query->clear();
			$query->select('r.title as resource_name')
				->from('#__jongman_reservation_resources AS a')
				->join('LEFT','#__jongman_resources AS r on r.id=a.resource_id')
				->where('reservation_id = '.(int)$item->reservation_id);
			$dbo->setQuery($query);
			
			$items[$i]->resources = implode(',', $dbo->loadColumn());
			if ($workflow) {
				$items[$i]->participant_list = array();
				$items[$i]->invitee_list = array();
				$items[$i]->reservation_length = RFDateRange::create($items[$i]->start_date, $items[$i]->end_date, $timezone);
				if (WFApplicationHelper::isWorkflowEnabled('com_jongman.reservation', $items[$i]->reservation_id)) {
					$items[$i]->workflow_enabled = true;
					$items[$i]->workflow_state = WFApplicationHelper::getStateByContext('com_jongman.reservation', $items[$i]->reservation_id);
					$actions = WFApplicationHelper::getActions('com_jongman.reservation', $items[$i]->reservation_id);
				}else{
					$items[$i]->workflow_enabled = false;
					$items[$i]->workflow_state = new stdClass();
					$actions = RFApplicationHelper::getActions('com_jongman.resource.'.$items[$i]->resource_id);
				}
			}else{
				$items[$i]->workflow_enabled = false;
				$items[$i]->workflow_state = new stdClass();
				$actions = RFApplicationHelper::getActions('com_jongman.resource.'.$items[$i]->resource_id);	
			}
			
			$mine = $items[$i]->owner_id == $user->id;
			$items[$i]->access_delete = false;
			if ($actions->get('core.delete') || ($actions->get('core.delete.own') && $mine)) {
				$items[$i]->access_delete = true;
			}
			
			$items[$i]->access_change = false;
			if ($actions->get('core.edit') || ($actions->get('core.edit.own') && $mine)) {
				$items[$i]->access_change = true;
			}
		}
		
		return $items;
	}
	
	/**
	 * Get filtered resources
	 * @return array
	 */
	public function getResources()
	{
		$db 	= $this->getDbo();
		$query 	= $db->getQuery();
		
		$user 	= JFactory::getUser();
		
		// Return empty array if no project is select
		$schedule = (int) $this->getState('filter.schedule_id');
		
		$query->select('r.id AS value, r.title AS text')
			->from('#__jongman_resources');
		if ($schedule > 0) {
			$query->where('r.schedule_id='.$schedule);
		}
		
		$db->setQuery($query);
		return (array)$db->loadObjectList();
		 
	}
	/**
	 * Build a list of reservation authors	 *
	 * @return
	 */
	public function getOwners()
	{
		$db     = $this->getDbo();
		$query  = $db->getQuery(true);
		$user   = JFactory::getUser();
	
		// Return empty array if no project is select
		$schedule = (int) $this->getState('filter.schedule_id');
	
		if ($schedule <= 0) {
			// Make an exception if we are logged in...
			if ($user->id) {
				$item = new stdClass();
				$item->value = $user->id;
				$item->text  = $user->name;
	
				$items = array($item);
	
				return $items;
			}
	
			return array();
		}
	
		// Construct the query
		$query->select('u.id AS value, u.name AS text');
		$query->from('#__users AS u');
		$query->join('INNER', '#__jongman_reservations AS a ON a.owner_id = u.id');
	
		// Implement View Level Access
		if (!$user->authorise('core.admin', 'com_jongman')) {
			//$groups = implode(',', $user->getAuthorisedViewLevels());
			//$query->where('a.access IN (' . $groups . ')');
		}
	
		// Filter fields
		$filters = array();
		$filters['a.schedule_id'] = array('INT-NOTZERO', $this->getState('filter.schedule_id'));
	
		if ($this->getState('params')->get('approvalSystem') !=2) {
			$access = JongmanHelper::getActions('com_jongman');
			if (!$access->get('core.edit.state') && !$access->get('core.edit')) {
				$filters['a.state'] = array('STATE', '1');
			}
		}
		// Apply Filter
		RFQueryHelper::buildFilter($query, $filters);
	
		// Group and order
		$query->group('u.id');
		$query->order('u.name ASC');
	
		// Get the results
		$db->setQuery((string) $query);
		$items = (array) $db->loadObjectList();
	
		// Return the items
		return $items;
	}
	
	protected function getStoreId($id = '')
	{
		$id .= ':'.$this->getState('filter.schedule_id');
		$id .= ':'.$this->getState('filter.resource_id');
		$id .= ':'.$this->getState('filter.start_date');
		$id .= ':'.$this->getState('filter.end_date');
		$params = $this->getState('params');
		if ($params === null) {
			// $params === null as this class get called with ignore_request = true
			$params = JComponentHelper::getParams('com_jongman');
			$this->setState('params', $params);
		}
		if ($this->getState('params')->get('approvalSystem')==2) {
			$id .= ':'.$this->getState('filter.workflow_state_id');
		}else{
			$id .= ':'.$this->getState('filter.state');
		}
		return parent::getStoreId($id);
	}
	
	protected function getItemIdsByWorkflowState($workflowStateId = null, $context='com_jongman.reservation' )
	{
		if ($workflowStateId === null) return array();
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('item_id')
		->from('#__wf_instances')
		->where('workflow_state_id='.(int)$workflowStateId)
		->where('context='.$dbo->quote($context));
		$dbo->setQuery($query);
	
		return $dbo->loadColumn();
	}
	
}