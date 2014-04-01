<?php
/**
 * @version		$Id: schedules.php 432 2012-01-09 12:32:27Z mrs.siam $
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Methods supporting a list of schedules records.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jongman
 * @since		1.6
 */
class JongmanModelSchedules extends JModelList
{
	
	/**
	 * Constructor override.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @return  JongmanModelschedules
	 * @since   1.0
	 * @see     JModelList
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 's.id',
				'name', 's.name',
				'alias', 's.alias', 'view_days',
				'checked_out', 's.checked_out',
				'checked_out_time', 's.checked_out_time',
				'published', 's.published',
				'access', 's.access', 'access_level',
				'ordering', 's.ordering',
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
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			$this->context .= '.'.$layout;
		}
		// Load the filter state.
		$value = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '');
		$this->setState('filter.search', $value);

		$value = $this->getUserStateFromRequest($this->context.'.filter.access', 'filter_access', null, 'int');
		$this->setState('filter.access', $value);

		$value = $this->getUserStateFromRequest($this->context.'.filter.state', 'filter_published', '', 'string');
		$this->setState('filter.state', $value);

		// Load the parameters.
		$params = JComponentHelper::getParams('com_jongman');
		$this->setState('params', $params);

		// List state information.
		parent::populateState('s.name', 'asc');
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
        $user   = JFactory::getUser();

		// Select the required fields from the table.
		$query->select(
				's.id AS id, s.name AS name, s.alias AS alias,'.
                's.time_format as time_format,'.
                's.weekday_start, '.
                's.view_days AS view_days, s.layout_id, '.
				's.checked_out AS checked_out,'.
				's.checked_out_time AS checked_out_time,'.
				's.ordering AS ordering, s.published as published');
		
		$query->from('#__jongman_schedules AS s');

		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = s.access');   

		$query->select('lo.title as layout_title, lo.timezone as timezone');
		$query->join('LEFT', '#__jongman_layouts as lo ON lo.id=s.layout_id');
        
        // Implement View Level Access
        $access = $this->getState('filter.access');
        if (!empty($access)) {
        	$query->where('s.access = '.$access);
        }
	
		// Filter by published state
		$published = $this->getState('filter.state');
		if (is_numeric($published)) {
			$query->where('s.published = '.(int) $published);
		}else if ($published === '') {
			$query->where('(s.published = 0 OR s.published = 1)');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search)) {
			if (stripos($search, 'id:') === 0) {
				$query->where('s.id = '.(int) substr($search, 3));
			} else {
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
				$query->where('(s.name LIKE '.$search.' OR s.alias LIKE '.$search.')');
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->getState('list.ordering', 'name');
		$orderDirn	= $this->getState('list.direction', 'asc');
		if ($orderCol == 'ordering' || $orderCol == 'name') {
			$orderCol = 'name '.$orderDirn.', ordering';
		}
        if (empty($orderCol)) {
            $orderCol = 'name';
        }
		$query->order($db->escape($orderCol.' '.$orderDirn));
			
		return $query;
	}

	public function getItems()
	{	
		if ($items = parent::getItems()) {
			$db 	= $this->getDbo();
			$query 	= $db->getQuery(true);
			
			foreach ($items as $x => $item) {
				$query->clear();
				$query->select('*')
					->from('#__jongman_time_blocks as a')
					->where('layout_id ='.(int)$item->layout_id)
					->order('id ASC');
				$db->setQuery($query);
				$blocks = $db->loadObjectList();

				$timezone = $item->timezone;
				$layout = new RFLayoutSchedule($timezone);
				foreach ($blocks as $period) {
					if ($period->availability_code == 1) {
						$layout->appendPeriod(
							RFTime::parse($period->start_time), RFTime::parse($period->end_time), 
							$period->label, $period->day_of_week);
					}else{
						$layout->appendBlockedPeriod(
							RFTime::parse($period->start_time), RFTime::parse($period->end_time), 
							$period->label, $period->day_of_week);	
					}	
				}
				$items[$x]->layout = $layout; 						
				
			}
			return $items;
		}

		return false;
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
	public function getTable($type = 'Schedule', $prefix = 'JongmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

}