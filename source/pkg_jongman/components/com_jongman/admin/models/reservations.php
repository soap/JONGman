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
	protected function populateState($ordering = 're.start_date', $direction = 'asc')
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
		
		$reservationCategory = $this->getUserStateFromRequest($this->context.'.filter.reservation_category', 'filter_reservation_category', null, 'int');
		$this->setState('filter.reservation_category', $reservationCategory);
		
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
				're.id, re.alias as alias, re.title as title, '
				.'from_unixtime(re.start_date) as start_date, from_unixtime(re.end_date) as end_date,'
				.'re.checked_out, re.checked_out_time, re.resource_id,'
				.'re.state, re.access, re.created_time'
			)
		);
		$query->from('#__jongman_reservations AS re');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=re.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = re.access');

		// Join over the resources.
		$query->select('res.title AS resource_title');
		$query->join('LEFT', '#__jongman_resources AS res ON res.id = re.resource_id');

		// Join over the schedules.
		$query->select('sc.name AS schedule_title');
		$query->join('LEFT', '#__jongman_schedules AS sc ON sc.id = re.schedule_id');
				
		// Join over the users for the author.
		$query->select('ure.name AS reserved_by_name');
		$query->join('LEFT', '#__users AS ure ON ure.id = re.created_by');

		// Join over the users for the author.
		$query->select('urb.name AS reserved_for_name');
		$query->join('LEFT', '#__users AS urb ON urb.id = re.reserved_for');
		
		$reservationCategory = $this->getState('filter.reservation_category');
		if ($reservationCategory == 1) {
			$query->where('is_blackout <> 1');
		}else if ($reservationCategory == 2) {
			$query->where('is_blackout = 1');
		}
		
		if ($scheduleId = $this->getState('filter.schedule_id')) {
			$query->where('re.schedule_id ='.(int)$scheduleId);	
		}
		
		if ($resourceId = $this->getState('filter.resource_id')) {
			$query->where('re.resource_id ='.(int)$resourceId);	
		}
		
		if ($access = $this->getState('filter.access')) {
			$query->where('re.access = '.$access);
		}
		
		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		return $query;
	}
}