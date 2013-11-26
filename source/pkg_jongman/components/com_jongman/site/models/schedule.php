<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
require_once JPATH_COMPONENT."/libraries/dateutil.class.php";

class JongmanModelSchedule extends JModelItem {
	
	private $_scheduleId = null;
	
	private $_schedule = null; 
	
	private $_date_vars;
    
    function __construct($config = array())
    {
        parent::__construct($config);
        
        if (array_key_exists('type', $config)) 
        {
            $this->setState('scheduleType', $config['type']);
        }else{
        	// user is not login
        	if (JFactory::getUser()->get('id')) 
        	{
        		$this->setState('scheduleType', ALL);
        	}else{
        		$this->setState('scheduleType', READ_ONLY);
        	}
        }
             
        if (array_key_exists('id', $config)) 
        {
            $this->_scheduleId = $config['id'];
        }else{
        	$this->_scheduleId = JRequest::getInt('id');
        }
    }
	
    function populateState(){
    	// Load state from the request.
		
    	$pk = JRequest::getInt('id');
		$this->setState('schedule.id', $pk);
		
    	$params = JComponentHelper::getParams('com_jongman');
		
		$menuitemid = JRequest::getInt( 'Itemid' );
		$menu = JSite::getMenu();
		if ($menuitemid) {
			$menuparams = $menu->getParams( $menuitemid );
			$params->merge( $menuparams );
		}
		
		$this->setState('params', $params);
    }
    
	/**
	 * Method to get schedule data.
	 * @param	integer	The id of the schedule.
	 * @since	2.0	
	 * @return	mixed	item object on success, false on failure.
	 */
	public function &getItem($pk = null)
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('schedule.id');

		if ($this->_item === null) {
			$this->_item = array();
		}

		if (!isset($this->_item[$pk])) {
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->select("a.*, l.timezone")
				->from("#__jongman_schedules AS a")
				->join('LEFT', '#__jongman_layouts as l ON l.id=a.layout_id')
				->where("a.id = ".$pk);
			$db->setQuery($query);
			
			$this->_item[$pk] = $db->loadObject();
			if (isset($this->_item[$pk]->params)) {
				if (is_string($this->_item[$pk]->params)) {
					$params = new JRegistry($this->_item[$pk]->params);
					$this->_item[$pk]->params = $params;
					//merge to model state -> 'params'
					$params->merge($this->getState('params'));
					$this->setState('params', $params);	
				}	
			}
		}
		
		return $this->_item[$pk];			
	}
	
	/**
	 * 
	 * get schedule data
	 * @deprecated 2.0 use getItem instead
	 */
	public function getSchedule()
	{
		if (empty($this->_schedule)) 
		{
			$dbo = JFactory::getDbo();
			$query = $dbo->getQuery(true);
			$query->select("*")
				->from("#__jongman_schedules")
				->where("id = ".$this->_scheduleId);
			$dbo->setQuery($query);
			
			$this->_schedule = $dbo->loadObject();
		}
		
		return $this->_schedule;
	}
	
	/**
	 * 
	 * Get list of resources for this schedule
	 * @param int $pk
	 * @return array of resources
	 * @since 1.0
	 */
	public function getResources($pk = null) 
	{
		// Initialise variables.
		$pk = (!empty($pk)) ? $pk : (int) $this->getState('schedule.id');	
			
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		
		$query->select("id, title, status, ordering, published, params")
			->from("#__jongman_resources")
			->where("schedule_id = ".$pk)
			->order("ordering ASC");
		$db->setQuery($query);
		$resources = $db->loadObjectList();
		
		foreach ($resources as $x => $resource) {
			if (isset($resource->params) && !empty($resource->params)) {
				$resources[$x]->params = new JRegistry($resource->params);	
			}	
		}
		
		return $resources;
	}
	
	/**
	 * 
	 * Get list of reservation for this schedules
	 */
	public function getReservations()
	{
		$resourceids = $this->getResourceIds();
		
		if (is_array($resourceids)) $resource_ids = join(',', $resourceids);
        
		$_dv = $this->getDateVars();
		$start_date = $_dv['firstDayTs'];
		$end_date = $_dv['lastDayTs'];
		
		$dbo =  JFactory::getDbo();
		$query = $dbo->getQuery(true);
		 
        // If it starts between the 2 dates, ends between the 2 dates, or surrounds the 2 dates, get it
		$query->select("res.*");
		$query->from("#__jongman_reservations as res");
		
		$query->leftJoin("#__users as login ON res.reserved_for = login.id");
		$query->select("login.name as name");
		
		$query->where(
					"("
						."(start_date >= ".$start_date." AND start_date <= ".$end_date.")"
						." OR "
						."(end_date >= ".$start_date." AND end_date <= ".$end_date.")"
					.")"			
					." OR "
						."(start_date <= ".$start_date." AND end_date >= ".$end_date.")"
					);		
	

		if ($this->getState("scheduleType") == "RESERVATION_ONLY")
			$query->where("res.is_blackout <> 1");
		
		$query->where("res.resource_id IN (". $resource_ids . ")");
		
		$query->order("res.start_date, res.start_time, res.end_date, res.end_time");
        
		$dbo->setQuery($query);
        
        $rows = $dbo->loadObjectList();
        if (JError::isError($rows) || empty($rows) ) return array();
        
        $return = array();
		foreach ($rows as $row) {
			$index = $row->resource_id;
			$return[$index][] = $row;
		}
        
        return $return;		
	}
	
	/**
	 * Get date range object for selected schedule
	 * @return DateRange object
	 * @since 2.0
	 */
	public function getScheduleDates()
	{
		jimport('jongman.date.date');
		jimport('jongman.date.daterange');
		$schedule = $this->getItem();
		
		$user = JFactory::getUser();
		$userTimezone = $user->getParam('timezone', null);
		$tz = empty($userTimezone) ? JFactory::getConfig()->get('offset') : $userTimezone;
		$providedDate = JRequest::getCmd('sd', null);
		
		$date = empty($providedDate) ? JMDate::now() : new JMDate(preg_replace("/[a-zA-Z#]+/","",$providedDate), $tz);

		$selectedDate = $date->toTimezone($tz)->getDate();
		$selectedWeekday = $selectedDate->weekday();
		$scheduleLength = (int)$schedule->view_days;
		
		$startDay = $schedule->weekday_start;
		if ($startDay == 7) {
			$startDate = $selectedDate;
		}
		else{
			$adjustedDays = ($startDay - $selectedWeekday);
			if ($selectedWeekday < $startDay) {
				$adjustedDay = $adjustedDays - 7;
			}
			
			$startDate = $selectedDate->addDays($adjustedDays);
		}
		
		$applicableDates = new DateRange( $startDate, $startDate->addDays($scheduleLength-1) );
		
		return $applicableDates;
	}
	
	/**
	 * 
	 * get schedule layout
	 * @param unknown_type $pk
	 * @param unknown_type $tz
	 * @since 2.0
	 */
	protected function getScheduleLayout($pk = null, $tz = null)
	{
		jimport('jongman.date.time');
		
		$schedule = $this->getItem();
		// Get time blocks from database
		$blocks = $this->getTimeblocks($schedule->layout_id);

		if (empty($tz)) {
			$tz = $schedule->timezone;		
		}
		
		$layout = new ScheduleLayout($tz);

		foreach($blocks as $period) {
			if ($period->availability_code == 1) {
				$layout->appendPeriod(
					Time::parse($period->start_time), Time::parse($period->end_time), 
					$period->label, $period->day_of_week);
			}else{
				$layout->appendBlockedPeriod(
					Time::parse($period->start_time), Time::parse($period->end_time), 
					$period->label, $period->day_of_week);	
			}	
		}
		
		return $layout;
	}
	
	/**
	 * Get time blocks of selected schedule from database
	 * @since 2.0
	 */
	private function getTimeblocks($layout_id = null) 
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__jongman_time_blocks')
			->where('layout_id = '.(int)$layout_id)
			->order('id ASC');
			
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		return $rows;
		
	}

	/**
	 * 
	 * Get the reservation list 
	 * @since 2.0
	 */
	private function getReservationList()
	{
		jimport('jongman.application.schedule.reservationlisting');
		
		$timezone = JFactory::getUser()->getParam('offset');
		$list = new ReservationListing($timezone);
		
		//insert reservation data to the list

		return $list;
	}
	
	/**
	 * 
	 * Get daily layout object of the schedule reservation
	 * @since 2.0
	 */
	public function getDailyLayout()
	{
		jimport('jongman.application.schedule.dailylayout');
		
		$scheduleLayout = $this->getScheduleLayout();
		$reservationList = $this->getReservationList();
		
		$layout = new DailyLayout($reservationList, $scheduleLayout);
		
		return $layout;
	}
	
	/**
	 * 
	 * Get date variables used in building reservation in week calendar
	 * We assume UTC date from user, so converted to GMT as we store all date in GMT
	 * @return array date variables
	 * @deprecated 2.5
	 */
	public function getDateVars() 
	{
		if (!empty($this->_date_vars)) return $this->_date_vars;
		
		$schedule = $this->getItem();
		
		$default = false;

        $dv = array();

        // For Back, Current, Next Week clicked links
        // pull values into an array month,day,year
        $date = JRequest::getVar('date', null);
        $jump_date = JRequest::getVar('jump_date', null, 'post');
        if (!empty($jump_date)) $date = $jump_date;
        
        if (!empty($date)) {
        	list($year, $month, $day) = explode('-', $date);
        }	

        // Set date values if a date has been passed in (these will always be set to a valid date)
        if ( !empty($date) ) 
		{
            $dv['month']  = date('m', mktime(0,0,0, $month, 1));
            $dv['day']    = date('d', mktime(0,0,0, $dv['month'], $day));
            $dv['year']   = date('Y', mktime(0,0,0, $dv['month'], $dv['day'], $year));
        }
        else {
            // else set values to user defined starting day of week
            $d = getdate();
            $dv['month']  = $d['mon'];
            $dv['day']    = $d['mday'];
            $dv['year']   = $d['year'];
            $default = true;
        }

        // Make timestamp for today's date
        $dv['todayTs'] = mktime(0,0,0, $dv['month'], $dv['day'], $dv['year']);

        // Get proper starting day, 0=Sunday, 1=Monday
        $dayNo = date('w', $dv['todayTs']);

        if ($default) {
            // weekdayStart == 7 is current date
            if ($schedule->weekday_start < 7)
                $dv['day'] = $dv['day'] - ($dayNo - $schedule->weekday_start); // Make sure week starts on correct day
        }
        // If default view and first day has passed, move up one week
        // if ($default && (date(mktime(0,0,0,$dv['month'], $dv['day'] + $this->view_days, $dv['year'])) <= mktime(0,0,0)))

        $dv['firstDayTs'] = mktime(0,0,0, $dv['month'], $dv['day'], $dv['year']);
		$date_parts = getdate();

        // Make timestamp for last date
        // by adding # of days to view minus the day of the week to $day
        $dv['lastDayTs'] = mktime(0,0,0, $dv['month'], ($dv['day'] + $schedule->view_days - 1), $dv['year']);
        $dv['current'] = $dv['firstDayTs'];
        $dv['now'] = mktime(0,0,0);
		$this->_date_vars = $dv;
		
        return $this->_date_vars;	
	}
	
    /**
    * Get associative array of available times and rowspans
    * This function computes and returns an associative array
    * containing a timezone adjusted time value and it's rowspan value as
    * $array[time] => rowspan
    * @param none
    * @return array of time value and it's associated rowspan value
    * @deprecated 2.5
    */
    public function getTimeArray() 
    {
		$schedule = $this->getItem();
		
        $startDay = $startingTime = $schedule->day_start;
        $endDay   = $endingTime   = $schedule->day_end;
        $interval = $schedule->time_span;
        $timeHash = array();
        
        $user = JFactory::getUser();
        $userTz = new DateTimeZone( $user->getParam('timezone', JFactory::getApplication()->getCfg('offset')) );
		$scheduleTz = new DateTimeZone($schedule->timezone);
		$userDate = JFactory::getDate('now', $userTz);
		$scheduleDate = JFactory::getDate('now', $scheduleTz);
       
		$offset = $scheduleDate->getOffsetFromGMT(true) - $userDate->getOffsetFromGMT(true);
		
        // Compute the available times
        $prevTime = $startDay;

        if ( (($startDay % 60) != 0) && ($interval < 60) ) {
        	// Adjust time in minute to current user time zone
            $time = DateUtil::formatTime($startDay, true, $schedule->time_format, $offset);
            
            $timeHash[$time] = intval((60-($startDay%60))/$interval);
            $prevTime += $interval*$timeHash[$time];
        }

        while ($prevTime < $endingTime) {
            if ($interval < 60) {
            	// Adjust time in minute to current user time zone
            	$time = DateUtil::formatTime($prevTime, true, $schedule->time_format, $offset ); 

                $timeHash[$time] = intval(60 / $interval);
                $prevTime += 60;        // Always increment by 1 hour
            }
            else {
                $colspan = 1;           // Colspan is always 1
                // Adjust time in minute to current user time zone
               	$time = DateUtil::formatTime( $prevTime, true, $schedule->time_format, $offset ); 
                
				$timeHash[$time] = $colspan;
                $prevTime += $interval;
            }
        }
        return $timeHash;
    }
    
    /**
     * 
     * get Navigation object ...
     * @param none
     * @return navigation object
     * @since 2.0
     * @todo rewrite me to use library class method
     */
	public function getNavigation() 
	{
		require_once JPATH_COMPONENT.'/libraries/navigator.class.php';
		$schedule = $this->getItem();
		$date = $this->getDateVars();
		$navigator = new JongmanNavigator($date['firstDayTs'], $schedule->view_days);
		
		return $navigator;
			
	}

	/**
	 * 
	 * Get resource id for the selected schedule in array format
	 * @param unknown_type $pk
	 * @return array of StdObj
	 */
	private function getResourceIds($pk = null)
	{
		$pk = (!empty($pk)) ? (int) $pk : (int) $this->getState('schedule.id');
		$dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select("id")
        	->from("#__jongman_resources")
        	->where("schedule_id = ".$pk)
        	->where("published=1")
        	->order("ordering ASC");
        	
        $dbo->setQuery( $query );
        $resource_ids = $dbo->loadColumn();

        return $resource_ids;
	}
}