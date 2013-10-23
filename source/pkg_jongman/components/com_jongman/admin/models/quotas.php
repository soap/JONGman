<?php
// No direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.modellist');

/**
 * Quotas model.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       1.0
 */
class JongmanModelQuotas extends JModelList
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
	protected function populateState($ordering = 'quota_limit', $direction = 'asc')
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
				'q.id, q.title, q.alias, q.quota_limit, q.unit, q.duration, '.
				'q.checked_out, q.checked_out_time, q.schedule_id, '.
				'q.published, q.access, q.created_time '
			)
		);
		$query->from('#__jongman_quotas AS q');

		// Join over the schedules
		$query->select("IFNULL(sc.name,'*') as schedule_title");
		$query->join('LEFT', '#__jongman_schedules AS sc on sc.id=q.schedule_id');
		
		// Join over the resource
		$query->select("IFNULL(re.title,'*') as resource_title");
		$query->join('LEFT', '#__jongman_resources AS re on re.id=q.resource_id');

		// Join over the resource
		$query->select("IFNULL(gr.title,'*') as group_title");
		$query->join('LEFT', '#__usergroups AS gr on gr.id=q.group_id');
				
		// Join over the users for the checked out user.
		$query->select('uc.name AS editor');
		$query->join('LEFT', '#__users AS uc ON uc.id=q.checked_out');

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = q.access');


		// Join over the users for the author.
		$query->select('ua.name AS author_name');
		$query->join('LEFT', '#__users AS ua ON ua.id = q.created_user_id');

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');

		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		return $query;
	}
}