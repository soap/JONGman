<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

/**
 * Blackouts model.
 *
 * @package     JONGman
 * @subpackage  backend
 * @since       3.0
 */
class JongmanModelBlackouts extends JModelList
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 * @since   3.0
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
	 * @since   3.0
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);
	
		// Select the required fields from the table.
		$query->select( $this->getState('list.select', 'a.*') );
		$query->from('#__jongman_blackout_instances AS a');
		
		$query->select('bs.alias, bs.state as state, bs.owner_id, bs.title, bs.description, bs.repeat_type, bs.repeat_options, bs.checked_out, bs.checked_out_time');
		$query->join('INNER', '#__jongman_blackouts AS bs ON a.blackout_id=bs.id');
		
		$query->join('INNER', '#__jongman_blackout_resources AS bsr ON a.blackout_id=bsr.blackout_id');
		
		$query->select('r.schedule_id');
		$query->join('INNER', '#__jongman_resources AS r ON bsr.resource_id=r.id');
		$query->select('sc.name as schedule_name');
		$query->join('INNER', '#__jongman_schedules AS sc ON r.schedule_id=sc.id');

		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=bs.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = bs.access');

		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = bs.created_by');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.start_date');
		$orderDirn	= $this->state->get('list.direction', 'ASC');
		if (!empty($orderCol)) {
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}
		
		return $query;
	}
}