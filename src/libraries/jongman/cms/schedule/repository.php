<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
jimport('jongman.base.ischedulerepository');

class RFScheduleRepository implements IScheduleRepository
{
	public function loadById($scheduleId)
	{
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_jongman/tables');
		$table = JTable::getInstance('Schedule', 'JongmanTable');
		$table->load($scheduleId);
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('timezone')->from('#__jongman_layouts')->where('id='.$table->layout_id);
		$db->setQuery($query);
		
		$table->timezone = $db->loadResult();
		
		
		$schedule = RFSchedule::fromRow($table);

		return $schedule;
	}
	
	public function getAll()
	{
		$schedules = array();
	
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$query->select('s.*')
			->from('#__jongman_schedules AS s')
			->select('l.timezone as timezone')
			->join('LEFT', '#__jongman_layouts AS l on l.id=s.layout_id');
		$dbo->setQuery($query);
		$rows = $dbo->loadObjectList();
		
		foreach($rows as $row) 
		{
			$schedules[] = RFSchedule::fromRow($row);
		}
		
		return $schedules;
	}
	
	/**
	 * Get schedule layout
	 * @see IScheduleRepository::getLayout()
	 */
	public function getLayout($scheduleId, ILayoutFactory $layoutFactory)
	{
		/**
		 * @var $layout RFLayoutSchedule
		 */
		$layout = $layoutFactory->createLayout();
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$query->select('tb.label, tb.end_label, tb.start_time, tb.end_time, tb.availability_code, tb.day_of_week')
			->from('#__jongman_time_blocks AS tb')
			
			->select('l.timezone')
			->join('inner', '#__jongman_layouts AS l ON tb.layout_id = l.id')
			->join('inner', '#__jongman_schedules AS s ON l.id = s.layout_id ')
			
			->where('s.id='.$scheduleId)
			->order('tb.start_time ASC');
		
		$dbo->setQuery($query);
		$rows = $dbo->loadObjectList();
		
		foreach ($rows as $row)
		{
			$timezone = $row->timezone;
			$start = RFTime::parse($row->start_time, $timezone);
			$end = RFTime::parse($row->end_time, $timezone);
			$label = $row->label;
			$periodType = $row->availability_code;
			$dayOfWeek = $row->day_of_week;
	
			if ($periodType == RFPeriodTypes::RESERVABLE)
			{
				$layout->appendPeriod($start, $end, $label, $dayOfWeek);
			}
			else
			{
				$layout->appendBlockedPeriod($start, $end, $label, $dayOfWeek);
			}
		}
	
	
		return $layout;
	}
}