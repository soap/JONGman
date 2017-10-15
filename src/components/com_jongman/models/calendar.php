<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
/**
 * The Jongman Calendar model.
 *
 * @package     JONGman
 * @subpackage  site
 * @since       3.0
*/
class JongmanModelCalendar extends JModelItem
{
	protected $factory;
	protected static $calendar;
	protected static $selectedSchedule;
	protected static $selectedResourceId;
	protected static $filters;
	protected $selectedGroup;
	
	private $scheduleRepository;
	private $resourceService;
	private $reservationRepository;
	
	public function __construct($config = array())
	{
		$this->factory = new RFFactoryCalendar();
		$this->scheduleRepository = new RFScheduleRepository();
		$this->reservationRepository = new RFReservationViewRepository();	
		
		$resourceRepository = new RFResourceRepository();
		$this->resourceService = new RFResourceService($resourceRepository);
		parent::__construct($config);
	}

	protected function populateState()
	{
		$user = JFactory::getUser();
		$app = JFactory::getApplication();
		
		$scheduleId = $app->getUserStateFromRequest('com_jongman.calendar.schedule_id', 'sid', null);
		$this->setState('schedule_id', $scheduleId);
		
		$resourceId = $app->input->getInt('rid', null);
		$this->setState('resource_id', $resourceId);
		
		$selectedGroupId = $app->getUserStateFromRequest('com_jongman.calendar.group_id', 'gid', null);
	}
	
	public function getItem()
	{
		return $this->getCalendar();	
	}


	public function getCalendar()
	{
		if (!empty(self::$calendar)) return self::$calendar;
		
		$input = JFactory::getApplication()->input;
		$type = $input->getCmd('caltype', 'month');
		
		$year = $input->getInt('yy', null);
		$month = $input->getInt('mm', null);
		$day = $input->getInt('dd', null);
		$timezone = RFApplicationHelper::getUserTimezone();
		
		$defaultDate = RFDate::now()->toTimezone($timezone);
		
		if (empty($year))
		{
			$year = $defaultDate->year();
		}
		if (empty($month))
		{
			$month = $defaultDate->month();
		}
		if (empty($day))
		{
			$day = $defaultDate->day();
		}

		$user = JFactory::getUser();
		$schedules = $this->scheduleRepository->getAll();

		$showInaccessible = false; //Configuration::Instance()->GetSectionKey(ConfigSection::SCHEDULE, ConfigKeys::SCHEDULE_SHOW_INACCESSIBLE_RESOURCES, new BooleanConverter());
        $resources = $this->resourceService->getAllResources($showInaccessible, $user);

		$selectedScheduleId = $this->getState('schedule_id');
		$selectedSchedule = $this->getDefaultSchedule($schedules);

		$selectedResourceId = $this->getState('resource_id');
		$selectedGroupId = $this->getState('group_id');

		$resourceGroups = $this->resourceService->getResourceGroups($selectedScheduleId, $user);

		if (!empty($selectedGroupId))
		{
			$tempResources = array();
			$resourceIds = $resourceGroups->getResourceIds($selectedGroupId);
			$selectedGroup = $resourceGroups->getGroup($selectedGroupId);
			$this->selectedGroup = $selectedGroup;
		
			foreach ($resources as $resource)
			{
				if (in_array($resource->getId(), $resourceIds))
				{
					$tempResources[] = $resource;
				}
			}
		
			$resources = $tempResources;
		}
		
		if (!empty($selectedResourceId))
		{
			//$subscriptionDetails = $this->subscriptionService->forResource($selectedResourceId);
		}
		else
		{
			//$subscriptionDetails = $this->subscriptionService->forSchedule($selectedSchedule->getId());
		}
		
		$calendar = $this->factory->create($type, $year, $month, $day, $timezone, $selectedSchedule->getWeekdayStart());
		
		$reservations = $this->reservationRepository->getReservationList($calendar->firstDay(), $calendar->lastDay()->addDays(1),
				null, null, $selectedScheduleId,
				$selectedResourceId);
		
		/**
		 * @Fix Me:
		 * @todo What happen if we do not map reservation by resource
		 */
		$calendar->addReservations(RFCalendarReservation::fromScheduleReservationList(
						$reservations,
						$resources,
						$user,
						false)); //not group series by resource
		self::$filters = new RFCalendarFilters($schedules, $resources, $selectedScheduleId, $selectedResourceId, $resourceGroups);
		self::$calendar = $calendar;
		self::$selectedSchedule = $selectedSchedule;
		return $calendar;
	}
	
	public function getDisplayDate()
	{
		$calendar = $this->getCalendar();	
		return $calendar->firstDay();	
	}
	
	public function getScheduleId()
	{
		return self::$selectedSchedule->getId();		
	}
	
	public function getResourceId()
	{
		return $this->getState('resource_id');
	}
	
	public function getFilters()
	{
		if (empty(self::$filters))	{
			$calendar = $this->getCalendar();
		}
		
		return self::$filters;
	}
	
	public function getFirstDay()
	{	
		if (!empty(self::$selectedSchedule)) { 
			$selectedSchedule = self::$selectedSchedule;
		}else{
			$schedules = $this->scheduleRepository->getAll();
			$selectedSchedule = $this->getDefaultSchedule($schedules);
		}
		
		return $selectedSchedule->getWeekdayStart();
	}
	
	protected function getDefaultSchedule($schedules)
	{
		if (!empty(self::$selectedSchedule)) return self::$selectedSchedule;
		
		$default = null;
		$scheduleId = JFactory::getApplication()->getUserStateFromRequest('com_jongman.calendar.schedule_id', 'sid');

		/** @var $schedule Schedule */
		foreach ($schedules as $schedule)
		{
			if (!empty($scheduleId) && $schedule->getId() == $scheduleId)
			{
				return $schedule;
			}
		
			if ($schedule->getIsDefault())
			{
				$default = $schedule;
			}
		}

		return $default;		
	}
}