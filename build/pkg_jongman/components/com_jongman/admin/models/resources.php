<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of resource records.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jongman
 * @since		1.6
 */
class JongmanModelResources extends JModelList
{
	
	/**
	 * Constructor override.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @return  JongmanModelresources
	 * @since   1.0
	 * @see     JModelList
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'r.id',
				'title', 'r.title',
				'alias', 'r.alias',
				'checked_out', 'r.checked_out',
				'checked_out_time', 'r.checked_out_time',
				'schedule_id', 'r.schedule_id', 'schedule_title',
				'published', 'r.published',
				'access', 'r.access', 'access_level',
				'r.requires_approval', 'requires_approval',
				'ordering', 'r.ordering'
			);
		}

		parent::__construct($config);
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = 'ordering', $direction = 'asc')
	{
		// Initialise variables.
		$app = JFactory::getApplication('administrator');
		$layout = $app->input->getCmd('layout', null);
		if (!empty($layout)) $this->context = $this->context.'.'.$layout;
		
		// Load the filter state.
		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$accessId = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $accessId);

		$published = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $published);

		$scheduleId = $this->getUserStateFromRequest($this->context.'.filter.schedule_id', 'filter_schedule_id', '', 'string');
		$this->setState('filter.schedule_id', $scheduleId);
		
		// Load the parameters.
		$params = JComponentHelper::getParams('com_jongman');
		$this->setState('params', $params);

		// List state information.
		parent::populateState($ordering, $direction);
	}

    /**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
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
				'r.id AS id, r.title AS title, r.alias AS alias, r.schedule_id, '.
                'r.requires_approval as requires_approval, ' .
                //'r.min_reservation as min_reservation, r.max_reservation as max_reservation,'.
                //'r.min_notice_duration as min_notice_duration,'.
                //'r.max_notice_duration as max_notice_duration,'.
                
				'r.checked_out AS checked_out,'.
				'r.checked_out_time AS checked_out_time,'.
				'r.ordering as ordering, r.published as published, r.params')
		);
		$query->from('`#__jongman_resources` AS r');
        
		$query->select('s.name as schedule_name');
        $query->join('LEFT', '#__jongman_schedules as s ON r.schedule_id=s.id');

        $query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = r.access');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('r.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
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
			$query->where('r.published = ' . (int) $published);
		} else if ($published === '') {
			$query->where('(r.published = 0 OR r.published = 1)');
		}

		// Filter by a single or group of categories.
		$scheduleId = $this->getState('filter.schedule_id');
		if (is_numeric($scheduleId) && ($scheduleId != 0)) {
			$query->where('r.schedule_id = '.(int) $scheduleId);
		}
		else if (is_array($scheduleId)) {
			JArrayHelper::toInteger($scheduleId);
			$categoryId = implode(',', $scheduleId);
			$query->where('r.schedule_id IN ('.$scheduleId.')');
		}

		// Add the list ordering clause.
		$orderCol	= $this->getState('list.ordering', 'ordering');
		$orderDirn	= $this->getState('list.direction', 'asc');
        if (empty($orderCol)) {
            $orderCol = 'ordering';
        }
		$query->order($db->escape($orderCol.' '.$orderDirn));
		
		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		$id	.= ':'.$this->getState('filter.access');
		$id	.= ':'.$this->getState('filter.state');
		$id .= ':'.$this->getState('filter.schedule_id');

		return parent::getStoreId($id);
	}
    
	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param	type	The table type to instantiate
	 * @param	string	A prefix for the table class name. Optional.
	 * @param	array	Configuration array for model. Optional.
	 * @return	JTable	A database object
	 * @since	1.6
	 */
	public function getTable($type = 'Resource', $prefix = 'JongmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getItems()
	{
		$items = parent::getItems();
		if ($items === false) return false;
		
		foreach($items as $i => $item) {
			$items[$i]->params = new JRegistry($item->params);
			
			$items[$i]->hasMinNoticeTime = false;
			$minNoticeTime = (int) $items[$i]->params->get('min_notice_duration');
			if ( !empty($minNoticeTime)) {
				$items[$i]->hasMinNoticeTime = true;
				$items[$i]->minNoticeTime = RFTimeInterval::parse($minNoticeTime * 60);
			}
			$maxNoticeTime = (int) $items[$i]->params->get('max_notice_duration');
			$items[$i]->hasMaxNoticeTime = false;
			if ( !empty($maxNoticeTime)) {
				$items[$i]->hasMaxNoticeTime = true;
				$items[$i]->maxNoticeTime = RFTimeInterval::parse($maxNoticeTime * 60);
			}
			
			$items[$i]->hasMinDuration = false;
			$minDuration = (int) $items[$i]->params->get('min_reservation_duration');
			if ( !empty($minDuration)) {
				$items[$i]->hasMinDuration = true;
				$items[$i]->minDuration = RFTimeInterval::parse($minDuration * 60);
			}
			$maxDuration = (int) $items[$i]->params->get('max_reservation_duration');
			$items[$i]->hasMaxDuration = false;
			if ( !empty($maxDuration)) {
				$items[$i]->hasMaxDuration = true;
				$items[$i]->maxDuration = RFTimeInterval::parse($maxDuration * 60);
			}
		}
		
		return $items;
	}
}