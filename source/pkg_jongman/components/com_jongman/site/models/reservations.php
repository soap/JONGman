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
		
		$query->select('r.alias, r.titile, r.description, ' .
				'r.checked_out, r.checked_out_time, r.schedule_id, ' .
				'r.state, r.created_time');		
		$query->join('INNER','#__jongman_reservations AS r ON r.id=a.reservation_id');

		$query->select('sc.name as schedule_name');
		$query->join('LEFT', '#__jongman_schedules AS s ON s.id=r.schedule_id');
		
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=r.checked_out');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = r.created_by');
		
		// Join over the users for the owner.
		$query->select('uo.name AS owner_name');
		$query->join('LEFT', '#__users AS uo ON uo.id = r.owner_id');

		// Join over the resource.
		$query->select('rs.title AS resource_title, rs.access');
		$query->join('LEFT', '#__jongman_resources AS rs ON rs.id = r.resource_id');
		
		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = r.access');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		return $query;
	}
}