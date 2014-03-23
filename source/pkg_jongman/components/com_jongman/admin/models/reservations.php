<?php
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
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 * @since   1.0
	 */
	protected function populateState($ordering = 'a.title', $direction = 'asc')
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');

		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		$scheduleId = $this->getUserStateFromRequest($this->context.'.filter.schedule_id', 'filter_schedule_id', null, 'int');
		$this->setState('filter.schedule_id', $scheduleId);
		
		$resourceId = $this->getUserStateFromRequest($this->context.'.filter.resource_id', 'filter_resource_id', null, 'int');
		$this->setState('filter.resource_id', $resourceId);
		
		$reservationType = $this->getUserStateFromRequest($this->context.'.filter.reservation_type', 'filter_reservation_type', null, 'int');
		$this->setState('filter.reservation_type', $reservationType);
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_jongman');
		$this->setState('params', $params);		
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
				'a.id, a.alias as alias, a.title as title, a.owner_id, ' .
				'a.checked_out, a.checked_out_time, ' .
				'a.state, a.access, a.created'
			)
		);
		$query->from('#__jongman_reservations AS a');

		$query->select('i.start_date, i.end_date, i.reference_number');
		$query->join('LEFT', '#__jongman_reservation_instances as i on i.reservation_id=a.id');
		
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');
		
		// Join over the users for the owner user.
		$query->select('own.name AS owner');
		$query->join('LEFT', '#__users AS own ON own.id=a.owner_id');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id=a.access');

		// Join over the schedules.
		$query->select('sc.name AS schedule_title');
		$query->join('LEFT', '#__jongman_schedules AS sc ON sc.id=a.schedule_id');
				
		// Join over the users for the author.
		$query->select('au.name AS author');
		$query->join('LEFT', '#__users AS au ON au.id=a.created_by');
		
		if ($reservationType = $this->getState('filter.reservation_type')) {
			$query->where('a.type_id ='.(int)$reservationType);
		}
		/*
		if ($scheduleId = $this->getState('filter.schedule_id')) {
			$query->where('re.schedule_id ='.(int)$scheduleId);	
		}*/
		
		if ($access = $this->getState('filter.access')) {
			$query->where('a.access = '.$access);
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		return $query;
	}
	
	public function getItems()
	{
		$items = parent::getItems();
		if ($items === false) return false;
		
		return $items;
	}
	
}