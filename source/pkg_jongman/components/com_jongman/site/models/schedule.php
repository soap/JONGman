<?php
defined('_JEXEC') or die;

require_once JPATH_COMPONENT."/libraries/dateutil.class.php";

class JongmanModelSchedule extends JModel {
	
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
    	$params = JComponentHelper::getParams('com_jongman');
		
		$menuitemid = JRequest::getInt( 'Itemid' );
		$menu = JSite::getMenu();
		if ($menuitemid) {
			$menuparams = $menu->getParams( $menuitemid );
			$params->merge( $menuparams );
		}
		
		$this->setState('params', $params);
    }
    
	function setScheduleId($id)
	{
		$this->_scheduleId = $id;	
	}
	
	function getScheduleId()
	{
		return $this->_scheduleId;
	}
	
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
	
	public function getResources() 
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$query->select("id, title, status, need_approval, min_notice_time, max_notice_time, ordering, published")
			->from("#__jongman_resources")
			->where("schedule_id = ".$this->getScheduleId())
			->order("ordering ASC");
		$dbo->setQuery($query);
		
		return $dbo->loadObjectList();
	}
	
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
	 * 
	 * Get date variables used in building reservation in week calendar
	 * We assume UTC date from user, so converted to GMT as we store all date in GMT
	 * @return array date variables
	 */
	public function getDateVars() 
	{
		if (!empty($this->_date_vars)) return $this->_date_vars;
		
		$schedule = $this->getSchedule();
		
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
    * @global $conf
    */
    public function getTimeArray() 
    {
		$schedule = $this->getSchedule();
		
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
     */
	public function getNavigation() 
	{
		require_once JPATH_COMPONENT.'/libraries/navigator.class.php';
		$schedule = $this->getSchedule();
		$date = $this->getDateVars();
		$navigator = new JongmanNavigator($date['firstDayTs'], $schedule->view_days);
		
		return $navigator;
			
	}
	
	private function getResourceIds()
	{
		$dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select("id")
        	->from("#__jongman_resources")
        	->where("schedule_id = ".$this->getScheduleId()." AND published=1")
        	->order("ordering ASC");
        	
        $dbo->setQuery( $query );
        $resource_ids = $dbo->loadResultArray();

        return $resource_ids;
	}
}