<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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
	 * Constructor override.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @return  JongmanModelBlackouts
	 * @since   1.0
	 * @see     JModelList
	 */

	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
				'title', 'bs.title',
				'bs.alias', 'a.alias',
				'resource_title', 'bsr.resource_id', 'resource_id',
				'checked_out', 'bs.checked_out',
				'checked_out_time', 'bs.checked_out_time',
				'bs.state',
				'access', 'a.access', 'access_level',
				'ordering', 'a.ordering',
				'language', 'a.language',
				'created', 'bs.created',
				'created_by', 'bs.created_by',
				'modified', 'bs.modified',
				'modified_by', 'bs.modified_by',
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
	 * @since   3.0
	 */
	protected function populateState($ordering = 'a.start_date', $direction = 'asc')
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		$value = $app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $value);

		$value = $app->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $value);

		$value = $app->getUserStateFromRequest($this->context.'.filter.state', 'filter_state', '');
		$this->setState('filter.state', $value);

		$value = $app->getUserStateFromRequest($this->context.'.filter.schedule_id', 'filter_schedule_id');
		$this->setState('filter.schedule_id', $value);
		
		$value = $app->getUserStateFromRequest($this->context.'.filter.resource_id', 'filter_resource_id');
		$this->setState('filter.resource_id', $value);

		$value = $app->getUserStateFromRequest($this->context.'.filter.language', 'filter_language', '');
		$this->setState('filter.language', $value);

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
		
		$query->select('r.schedule_id, r.title as resource_title');
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
		
		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('a.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->escape($search, true).'%');
				$query->where('(bs.title LIKE '.$search.' OR bs.alias LIKE '.$search.')');
			}
		}

		// Filter by access level.
		if ($access = $this->getState('filter.access')) {
			$query->where('bs.access = ' . (int) $access);
		}

		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('bs.state = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(bs.state = 0 OR bs.state = 1)');
		}

		// Filter by a single resource
		$resourceId = $this->getState('filter.resource_id');
		if (is_numeric($resourceId) && $resourceId > 0) {
			$query->where('bsr.resource_id = '.(int) $resourceId);
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering', 'a.start_date');
		$orderDirn	= $this->state->get('list.direction', 'ASC');
		if (!empty($orderCol)) {
			$query->order($db->escape($orderCol.' '.$orderDirn));
		}
		
		return $query;
	}
}