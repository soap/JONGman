<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

/**
 * Reservations model.
 *
 * @package     JONGman
 * @subpackage  Frontend
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
				'id', 'a.id',
				'title', 'a.title',
				'alias', 'a.alias',
				'checked_out', 'a.checked_out',
				'checked_out_time', 'a.checked_out_time',
				'a.start_date', 'a.end_date', 
				'published', 'a.published',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'created', 'a.created',
				'created_by', 'a.created_by',
				'modified', 'a.modified',
				'modified_by', 'a.modified_by',
			);
		}
		
		if (isset($config['context'])) {
			$this->context = $config['context'];
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
	protected function populateState($ordering = 'a.start_date', $direction = 'asc')
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		
		// Params
		$value = $app->getParams('com_jongman');
		$this->setState('params', $value);
		
		// Adjust the context to support modal layouts.
		$layout = $app->input->getCmd('layout');
		
		// View Layout
		$this->setState('layout', $layout);
		if ($layout && $layout != 'print') $this->context .= '.' . $layout;

		$search = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$access = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);

		$state = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '');
		$this->setState('filter.state', $state);

		$start_date = $app->getUserStateFromRequest($this->context.'.filter.start_date', 'filter_start_date', '');
		$this->setState('filter.start_date', $start_date);
		
		$end_date = $app->getUserStateFromRequest($this->context.'.filter.end_date', 'filter_end_date', '');
		$this->setState('filter.end_date', $end_date);		
		
		$schedule_id = $app->getUserStateFromRequest($this->context.'.filter.schedule_id', 'filter_schedule_id', 0, 'int');
		$this->setState('filter.schedule_id', $schedule_id);
		
		$resource_id = $app->getUserStateFromRequest($this->context.'.filter.resource_id', 'filter_resource_id', 0, 'int');
		$this->setState('filter.resource_id', $resource_id);
		
		$type_id = $app->getUserStateFromRequest($this->context.'.filter.type_id', 'filter_type_id', 0, 'int');
		$this->setState('filter.type_id', $type_id);

		$owner_id = $app->getUserStateFromRequest($this->context.'.filter.owner_id', 'filter_owner_id', 0, 'int');
		$this->setState('filter.owner_id', $owner_id);
		
		$user_id = $app->getUserStateFromRequest($this->context.'.filter.user_id', 'filter_user_id', 0, 'int');
		$this->setState('filter.user_id', $user_id);

		$user_level = $app->getUserStateFromRequest($this->context.'.filter.user_level', 'filter_user_level', 1, 'int');
		$this->setState('filter.user_level', $user_level);
		
		// Filter - Is set
		$this->setState('filter.isset',
				(is_numeric($state) || !empty($search) || is_numeric($owner_id) ||
						is_numeric($resource_id))
		);

		// Set list state ordering defaults.
		parent::populateState($ordering, $direction);
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
				'a.id as instance_id, a.start_date, a.end_date, a.reference_number'
			)
		);
		$query->from('#__jongman_reservation_instances AS a');
		
		$query->select('r.id as reservation_id, r.alias, r.title as reservation_title, ' . 
				'r.description as reservation_description, ' .
				'r.owner_id, 0 as user_level, ' .
				'r.checked_out, r.checked_out_time, r.schedule_id, ' .
				'r.state, r.created');		
		$query->join('INNER','#__jongman_reservations AS r ON r.id=a.reservation_id');
		
		$query->select('s.name AS schedule_name');
		$query->join('LEFT', '#__jongman_schedules AS s ON s.id=r.schedule_id');
		
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=r.checked_out');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = r.created_by');
		
		// Join over the users for the owner.
		$query->select('uo.name AS owner_name, uo.email as owner_email');
		$query->join('LEFT', '#__users AS uo ON uo.id = r.owner_id');

		$query->select('rrs.resource_id');
		$query->join('INNER', '#__jongman_reservation_resources AS rrs ON rrs.reservation_id=a.reservation_id');
		
		// Join over the resource.
		$query->select('rs.title AS resource_title, rs.access');
		$query->join('LEFT', '#__jongman_resources AS rs ON rs.id = rrs.resource_id');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = r.access');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('r.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(r.title LIKE '.$search.' OR r.alias LIKE '.$search.')');
			}
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('r.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('r.state = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(r.state = -1 OR r.state = 0 OR r.state = 1)');
		}

		// Filter by a type id (reservation or blackout)
		$typeId = $this->getState('filter.type_id');
		if (!empty($typeId)) {
			$query->where('r.type_id = '.(int) $typeId);
		}
		else if (is_array($typeId)) {
			JArrayHelper::toInteger($typeId);
			$typeId = implode(',', $typeId);
			$query->where('r.type_id IN ('.$typeId.')');
		}
		
		$scheduleId = $this->getState('filter.schedule_id');
		if (!empty($scheduleId)) {
			$query->where('rs.schedule_id = '.(int)$scheduleId);
		}
		
		$resourceId = $this->getState('filter.resource_id');
		if (!empty($scheduleId)) {
			$query->where('rs.id = '.(int)$scheduleId);
		}
		
 		$userId = $this->getState('filter.user_id');
 		$userLevel = $this->getState('filter.user_level', 1);
 		if (!empty($userId)) {
 			// @todo use user_id from #__jongman_reservation_users
 			$query->where('a.id IN (SELECT reservation_instance_id FROM #__jongman_reservation_users WHERE user_id = '.(int)$userId.' AND user_level='.$userLevel.')');
 		}
		
		$userTz = JongmanHelper::getUserTimezone();
		$startDate = $this->getState('filter.start_date');
		$endDate = $this->getState('filter.end_date');
		
		if (!empty($startDate) && !empty($endDate)) {
			$start_date = $db->quote(JDate::getInstance($startDate, $userTz)->toSql());
			$end_date = $db->quote(JDate::getInstance($endDate, $userTz)->toSql());
			
			$query->where(
				'((a.start_date >='.$start_date.' AND a.start_date <='.$end_date.') OR ' .
				'(a.end_date >= '.$start_date.' AND a.end_date <='.$end_date.') OR ' .
				'(a.start_date <= '.$start_date.' AND a.end_date >='.$end_date.'))'
				);	
		}
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.start_date');
		$orderDirn	= $this->state->get('list.direction', 'ASC');

		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		$timezone = JongmanHelper::getUserTimezone();
		
		$params = $this->getState('params');
		if ($params === null) {
			// $params === null as this class get called with ignore_request = true
			$app = JFactory::getApplication();
			$params = $app->getParams();
			$this->setState('params', $params);
		}
		$workflow = (bool)($this->getState('params')->get('approvalSystem') == 2);
		foreach($items as $i => $item) {
			$items[$i]->participant_list = array();
			$items[$i]->invitee_list = array();
			$items[$i]->reservation_length = RFDateRange::create($items[$i]->start_date, $items[$i]->end_date, $timezone);
			if ($workflow) {
				$items[$i]->workflow_state = WFApplicationHelper::getStateByContext('com_jongman.reservation', $items[$i]->reservation_id);
			}else{
				$items[$i]->workflow_state = new stdClass();
			}
		}
		
		return $items;
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
		//$access = PFtasksHelper::getActions();
	
		// Return empty array if no project is select
		$schedule = (int) $this->getState('filter.schedule');
	
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
		if (!$user->authorise('core.admin', 'com_pftasks')) {
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
	
		// Filter fields
		$filters = array();
		$filters['a.schedule'] = array('INT-NOTZERO', $this->getState('filter.schedule'));
	
		if (!$access->get('core.edit.state') && !$access->get('core.edit')) {
			$filters['a.state'] = array('STATE', '1');
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
	
}