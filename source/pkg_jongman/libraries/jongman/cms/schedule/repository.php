<?php
defined('_JEXEC') or die;
jimport('jongman.base.ischedulerepository');

class RFScheduleRepository implements IScheduleRepository
{
	public function loadById($scheduleId)
	{
		$config = array('ignore_request'=>true);
		$model = JModelLegacy::getInstance('Schedule', 'JongmanModel', $config);
		$model->setState('schedule.id', $scheduleId);
		$item = $model->getItem();

		$schedule = RFSchedule::fromRow($item);
		return $schedule;
	}
	
	public function getAll()
	{
		$schedules = array();
	
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$query->select('s.*')
			->from('#__jongman_schedules AS s');
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