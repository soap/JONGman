<?php
defined('_JEXEC') or die;
// Import dependancies.
jimport('joomla.application.component.view');
require_once(JPATH_ADMINISTRATOR.'/includes/toolbar.php');
require_once(JPATH_COMPONENT.'/libraries/summary.class.php');

/**
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  Frontend
 * @since       1.0
 */
class JongmanViewSchedule extends JView
{
	/**
	 * @var    record of schedule
	 * @since  1.0
	 */
	protected $schedule;
	protected $datevars;
	protected $resources;
	protected $reservations;
	/**
	 * @var    The pagination object for the list.
	 * @since  1.0
	 */
	protected $navigation;

	/**
	 * @var    JObject	The model state.
	 * @since  1.0
	 */
	protected $state;

	/**
	 * Prepare and display the reservations view.
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function display()
	{
		if ($this->getLayout()=='error') {
			parent::display();
			return true;
		}
		// Initialise variables.
		$this->schedule		= $this->get('Schedule');
		$this->datevars		= $this->get("DateVars");
		$this->timearray	= $this->get("TimeArray");
		$this->resources	= $this->get("Resources");
		$this->reservations = $this->get("Reservations");
		
		$this->navigation	= $this->get('Navigation');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display();

	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   1.0
	 */
	protected function addToolbar()
	{
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/helpers/jongman.php';
		$doc = JFactory::getDocument();
		$doc->addStyleSheet(JURI::root(true).'/media/com_jongman/css/toolbar.css');
		$doc->addStyleSheet(JURI::root(true).'/media/com_jongman/css/jongman.css');
		
		// Initialise variables.
		$state	= $this->get('State');
		$canDo	= JongmanBackendHelper::getActions();

		JToolBarHelper::title(JText::_('COM_JONGMAN_RESERVATIONS_TITLE'));

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('reservation.add', 'COM_JONGMAN_TOOLBAR_NEW');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('reservation.edit', 'COM_JONGMAN_TOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::publishList('reservations.publish', 'COM_JONGMAN_TOOLBAR_PUBLISH');
			JToolBarHelper::unpublishList('reservations.unpublish', 'COM_JONGMAN_TOOLBAR_UNPUBLISH');
		}

		if ( $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'reservations.delete','COM_JONGMAN_TOOLBAR_DELETE');
		} 
	}
	
	/**
	* Whether the reservation link is shown/clickable
	* @param bool $viewable_date if the date is viewable
	* @param array $current_record the currently iterated resource record
	* @return if this reservation link is available to view
	*/
    function canClickReservation($viewable_date, $current_record) {	

		$is_active = ($current_record->published == 1);
		$perms = JongmanHelper::getActions('com_jongman.resource.'.$current_record->id);
		$has_permission = false;
		if ( $perms->get('core.edit')
				|| $perms->get('core.delete')
				|| $perms->get('core.edit.state') ) {
			$has_permission = true;  
		}
		
		return ( $viewable_date && $is_active && $has_permission );
    }

	/**
	* Whether the reservation link is shown for user to add reservation for current resource
	* @param bool $viewable_date if the date is viewable
	* @param array $current_record the currently iterated resource record
	* @return if this reservation link is available to view
	*/
    function canAddReservation($viewable_date, $current_record) {	

		$is_active = ($current_record->published == 1);
		$perms = JongmanHelper::getActions('com_jongman.resource.'.$current_record->id);
		
		$has_permission = false;
		if ( $perms->get('core.create') ) {
			$has_permission = true;  
		}

		return ( $viewable_date && $is_active && $has_permission );
    }
	/**
	* Whether the reservation link is shown/clickable on this date
	* @param int $current_date the current datestamp
	* @param int $min_notice the minimum number of notice hours for the current resource
	* @param int $max_notice the maximum number of notice hours for the current resource
	* @return if this reservation link is available to view
	*/
    function isViewableDate($current_date, $min_notice, $max_notice) {
    	$min_notice = (int)$min_notice;
    	$max_notice = (int)$max_notice;
    	
    	if ($min_notice == 0 && $max_notice == 0) return true;
		
    	$min_days = intval($min_notice / 24);

		$min_date = mktime(0,0,0, date('m'), date('d') + $min_days);
		
		if (( $min_notice != 0) && ($current_date < $min_date))
		{
			return false;
		}

		if ($max_notice != 0) {
			$max_days = ceil($max_notice / 24);
	
			$max_date = mktime(0,0,0, date('m'), date('d') + $max_days);
			
			if ($current_date > $max_date)
			{
				return false;
			}
		}

		return true;
    }

    /**
    * Print out the reservations for each resource on each day
    * @param none
    */
    function print_reservations() {
        if (!$this->resources) return;
        $current_date = $this->datevars['current']; // Store current_date so we dont have to access the array every time

        // Repeat this whole process for each resource in the database (in schedule)
        for ($count = 0; $count < count($this->resources); $count++) {
            $prevTime = $this->startDay;        // Previous time holder
            $totCol = intval(($this->schedule->day_end - $this->schedule->day_start) / $this->schedule->time_span);    // Total columns holder
			$cur_resource = $this->resources[ $count ];

            // Store info about this current resource in local vars
            $id = $cur_resource->id;
            $name = $cur_resource->title;
            $status = $cur_resource->status;
            $approval = $cur_resource->need_approval;

            $shown = false;        // Default resource visiblilty to not shown
			$viewable_date = $this->isViewableDate($current_date, $cur_resource->min_notice_time, $cur_resource->max_notice_time);

            // If the date has not passed, resource is active and user has permission,
            //  or the user is the admin allow reservations to be made
            $shown = $this->canShowReservation($viewable_date, $cur_resource);

			if ($this->scheduleType == READ_ONLY)
			{
				$color = 'ro' . ($count%2);
			}
			else
			{
            	$color = 'r' . ($count%2);
			}
            JongmanHelper::print_name_cell($current_date, $id, $name, $shown, $this->scheduleType == BLACKOUT_ONLY, $this->scheduleId, $approval, $color);

            $index = $id; //resource id
            if (isset($this->reservations[$index])) {
                
                for ($i = 0; $i < count($this->reservations[$index]); $i++) {
                    /** FIXED by Prasit Gebsaap: 
                        For PHP5, we have to clone it, otherwise it will overwrite orginal reservation properties **/
                    $rs = clone $this->reservations[$index][$i];

                    // If it doesnt start sometime today, end sometime today, or surround today, just skip over it
                    if (
                        !(($rs->start_date >= $current_date && $rs->start_date <= $current_date)
                        || ($rs->end_date >= $current_date && $rs->end_date <= $current_date)
                        || ($rs->start_date <= $current_date && $rs->end_date >= $current_date))
                       ) {
                        continue;
                    }

                    // Just skip the reservation if the ending date/time is todays start time
                    if ($rs->end_date == $current_date && $rs->end_time == $this->startDay) { continue; }

                    // If the reservation starts before or ends after todays date, just pretend it ends today so it shows correctly
                    if ($rs->start_date < $current_date) {
                        $rs->start_time = $this->startDay;
                    }
                    if ($rs->end_date > $current_date) {
                        $rs->end_time = $this->endDay;
                    }

                    // Print out row of reservations
                    $thisStart = $rs->start_time;
                    $thisEnd = $rs->end_time;

                    if ($thisStart < $this->startDay && $thisEnd > $this->startDay)
                        $thisStart = $this->startDay;
                    else if ($thisStart < $this->startDay && $thisEnd <= $this->startDay)
                        continue;    // Ignore reservation, its off the schedule

                    if ($thisStart < $this->endDay && $thisEnd > $this->endDay)
                        $thisEnd = $this->endDay;
                    else if ($thisStart >= $this->endDay && $thisEnd > $this->startDay)
                        continue;    // Ignore reservation, its off the schedule

                    $colspan = intval(($thisEnd - $thisStart) / $this->timeSpan);

                    $this->move_to_starting_col($rs, $thisStart, $prevTime, $this->timeSpan, $id, $current_date, $shown, $color);

                    if ($rs->is_blackout == 1)
                        $this->write_blackout($rs, $colspan);
                    else
                        $this->write_reservation($rs, $colspan, $viewable_date);

                    // Set prevTime to this reservation's ending time
                    $prevTime = $thisEnd;
                }
            }

            $this->finish_row($this->endDay, $prevTime, $this->timeSpan, $id, $current_date, $shown, $color);
        }
    }
    /**
    * Return color_select for given reservation
    * @param array $rs object of reservation information
    */
    function getReservationColorStr($rs) {

        $is_mine = false;
		$is_participant = false;
        $is_past = false;
        $color_select = 'other_res'; // Default color (if anything else is true, it will be changed)

        /*
         * We did not use reservation_users as it is in PHPScheduleIt, 
         * but we store owner in reserved_for field 
         */
        $my = JFactory::getUser();
		if ($this->state->get('scheduleType') != READ_ONLY) {
            if ($rs->reserved_for == $my->id) 
            /* if($rs->owner == 1)*/  {
                $is_mine = true;
                $color_select = 'my_res';
            }
			/*else if ($rs->participantid != null && $rs->reserved_for == 0) { //Will be fixed later
				$is_participant = true;
				$color_select = 'participant_res';
			}*/
        }

        if (mktime(0,0,0) > $this->datevars['current']) { // If todays date is still before or on the day of this reservation
            $is_past = true;
			if ($is_mine) {
				 $color_select = 'my_past_res';
			}
			else if ($is_participant) {
				$color_select = 'participant_past_res';
			}
			else {
				$color_select ='other_past_res';
			}
        }

        if ( $rs->state == 1 ) {
            $color_select = 'pending';
        }

        return $color_select;
    }

    /**
    * Calculates and calls the template function to print out leading columns
	* @param array $rs array of reservation information
    * @param int $start starting time of reservation
    * @param int $prev previous ending reservation time
    * @param int $span time span for reservations
    * @param string $machid id of the resource on this table row
    * @param int $ts timestamp for the reservation start date
    * @param bool $clickable if this row's cells can be clicked to start a reservation
	* @param string $color class of column background
    */
    function move_to_starting_col($rs, $start, $prev, $span, $machid, $ts, $clickable, $color) {
        $cols = (($start-$prev) / $span) - 1;
		JongmanHelper::print_blank_cols($cols, $prev, $span, $ts, $machid, $this->schedule->id, $this->param->get('scheduleType'), $clickable, $color);
    }

    /**
    * Calculates and calls template function to print out trailing columns
    * @param int $end ending time of day
    * @param int $prev previous ending reservation time
    * @param int $span time span for reservations
    * @param string $machid id of the resource on this table row
    * @param int $ts timestamp for the reservation start date
    * @param bool $clickable if this row's cells can be clicked to start a reservation
	* @param string $color class of column background
    */
    function finish_row($end, $prev, $span, $machid, $ts, $clickable, $color) {
        global $conf;
        $cols = (($end-$prev) / $span) - 1;
		
		JongmanHelper::print_blank_cols($cols, $prev, $span, $ts, $machid, $this->scheduleId, $this->scheduleType, $clickable, $color);
        JongmanHelper::print_closing_tr();
    }

    /**
    * Calls template function to write out the reservation cell
    * @param object $rs object of reservation information
    * @param int $colspan column span value
	* @param bool $viewable_date if the date is clickable/viewable
    */
    function writeReservation($rs, $colspan, $viewable_date) {
        $perms = JongmanHelper::getActions('com_jongman.resource.'.$rs->resource_id);
    	$params = $this->state->get("params");
        $is_mine = false;
        $is_past = false;
        $is_admin = $perms->get('core.admin');
        	 
		$is_private = $params->get('privateReservation', 0) && !$is_admin;
        $color_select = $this->getReservationColorStr($rs);
		$scheduleType = $this->state->get('scheduleType');

        if ($scheduleType != READ_ONLY) {
        	$user = & JFactory::getUser();
            if ( ($rs->reserved_for == $user->get('id')) || ($rs->created_by == $user->get('id') ) ){
                $is_mine = true;
            }
        }
        $config = array('title'=>$rs->title, 
        	'userName'=>JFactory::getUser($rs->reserved_for)->name,
        	'cellDisplay'=>$params->get('reservationBarDisplay'), 
        	'text'=>$rs->description);
		$summary = new JongmanSummary($config);

        // If this is the user who made the reservation or the admin,
        //  and time has not passed, allow them to edit it
        //  else only allow view
        
        if ( ($is_mine && $perms->get('com_jongman.edit.own') && $viewable_date) || $is_admin ) {
        	$mod_view = 'm';	 	
       	}else{
       		$mod_view = 'v';
       	}
       	if ($is_private && !$is_admin) {
       		$showSummary = false;
       		$viewable = false;
       	}else{
        	$showSummary = ($scheduleType != READ_ONLY || ($scheduleType == READ_ONLY && $params->get('readOnlySummary', 1) )) ;
        	$viewable = ($scheduleType != READ_ONLY || ($scheduleType == READ_ONLY && $params->get('readOnlyDetail', 1)));
       	}
       
        $summary->setVisible((bool)$showSummary);
		
		$is_pending = ($rs->state == 1);

		JongmanHelper::writeReservation($colspan, $color_select, $mod_view, $rs->id, $summary, $viewable, $scheduleType == READ_ONLY, $is_pending);
    }

    /**
    * Calls template function to write out the blackout cell
    * @param array $rs array of reservation information
    * @param int $colspan column span value
    */
    function writeBlackout($rs, $colspan) {
        $params = & JComponentHelper::getParams('com_jongman');
		$is_private = $params->get('privacyMode', false) && !JongmanHelper::isAdmin();
        $is_readOnly = $params->get('readOnlySummary');
        $showsummary = (($this->scheduleType != READ_ONLY || ($this->scheduleType == READ_ONLY && $is_readOnly)) && $this->showSummary && !$is_private);
		
        $summary = new JongmanSummary($rs->summary);
        $summary->visible = $showsummary;
        
        JongmanHelper::write_blackout($colspan, JongmanHelper::isAdmin(), $rs->id, $summary,  $showsummary);
    }	
}