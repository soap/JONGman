<?php
/**
 * @version: $Id$
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class JongmanHelper 
{
	
	function isAdmin( $uid = null, $resource_id = null )
	{
		if (empty($uid)) {
			$user = JFactory::getUser();
		}else{
			$user = JFactory::getUser($uid);
		}
		
		if (!empty($resource_id)) {
			$assetName = 'com_jongman.resource.'.$resource_id;
		}else{
			$assetName = 'com_jongman';
		}
		return $user->authorise('core.admin', $assetName);
	}

    function getUserSelectList($tagname, $attribs = '', $selected)
    {
        $dbo = & JFactory::getDbo();
        $sql = "SELECT id as value, name as text FROM #__users WHERE block = 0";
        $dbo->setQuery($sql);
        $rows = $dbo->loadObjectList();
        
  		$html = JHTML::_('select.genericlist', $rows, $tagname, $attribs, 'value', 'text', $selected);
		
		return $html;   
    }
	/**
	 * 
	 * Get allowed actions for current user
	 * @param string $assetName
	 * @return JObject each action is property with boolean atrribute 
	 */    
	public static function getActions($assetName = 'com_jongman')
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete',
			'com_jongman.reservation.create', 'com_jongman.reservation.edit', 'com_jongman.reservation.edit.state', 
			'com_jongman.reservation.edit.own', 'com_jongman.reservation.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}   
	
	/**
	 * 
	 * Get schedule display header in specified format
	 * @param string $name
	 * @param unix_timestamp $firstDate
	 * @param unix_timestamp $lastDate
	 * @param string $headerDateFormat
	 * @param boolaen $showLastDate
	 */
	public static function getScheduleHeader($name, $firstDate, $lastDate, $headerDateFormat, $showLastDate=true)
	{
		$displayTitle = $name."&nbsp;&nbsp;".JHtml::date($firstDate,$headerDateFormat, null);
		if ($showLastDate) {
			$displayTitle .= " - ".JHtml::date($lastDate, $headerDateFormat, null);
		}
		return $displayTitle;
	}
	 
	/**
	* Formats and returns the time header of the table (it is the same for every one)
	* @param array $th array of time values and their rowspans
	* @param int $startDay starting time of day
	* @param int $endDay ending time of day
	* @param int $timespan time intervals
	* @global $conf
	*/
	function getHourHeader( $th, $startDay, $endDay, $timespan ) 
	{	
		$header = '';

    	// Write out the available times
    	foreach ($th as $time => $cols) {
        	$header .= "<td colspan=\"$cols\">$time</td>";
   	 	}

    	// Close row, start next
    	$header .= "</tr>\n<tr class=\"scheduleTimes\">";

    	// Compute total # of cols
    	$totCol = intval(($endDay - $startDay) / $timespan);
    	$width = (85/$totCol);
    	// Create the fraction hour minute marks
    	for ($x = 0; $x < $totCol; $x++)
    	{
        	$header .= "<td width=\"$width%\">&nbsp;</td>";
    	}

    	return $header;
	}
	
	/**
	* Start table for one day on schedule
	* This function starts the table for each day
	* on the schedule, printing out it's date
	* and the time value cells
	* @param string $displayDate date string to print
	*/
	function getStartDayTable( $displayDate, $hour_header, $isCurrentDate ) 
	{
		return 
			'<table class="day-outer">
			    <tr>
				   <td>
                     <table class="day-inner">
			             <tr class="scheduleTimes">
			                <td rowspan="2" width="15%" class="'.($isCurrentDate ? 'scheduleDateCurrent' : 'scheduleDate').'">'.$displayDate.'</td>'
                            .$hour_header.'</tr>';
	}
	
	function getEndDayTable()
	{
		return '</table></td></tr></table><br />';
	}

	/**
	* Prints out the cell containing all the resource information
	* @param int $ts timestamp for the current day
	* @param int $id id of this resource
	* @param string $name name of this resource
	* @param boolean $clickable whether this resource can be reserved
	* @param boolean $is_blackout whether this is a blackout schedule or not
	* @param int $scheduleid id of the current schedule
	* @param boolean $pending is reservation pending approval
	* @param string $color background color of row
	*/
	function print_name_cell($ts, $id, $name, $clickable, $is_blackout, $scheduleid, $pending = false, $color = '') 
    {
        $url = 'index.php?option=com_jongman&task=reservation.add&type=r&tmpl=component&resource_id='.$id.'&schedule_id='.$scheduleid.'&ts='.$ts.'&is_blackout='.$is_blackout;
        $color = (empty($color)) ? 'r0' : $color;
    	$attribs = array("class"=>"modal", "rel"=>"{handler:'iframe', closeWithOverlay:false, size:{x:800, y:500}}");

    	// Start a new row and print out resource name
    	echo "\n<tr class=\"$color\"><td class=\"resourceName\">";
        // (type, resourceid, start_date, reservationid, scheduleid, is_blackout, read_only, pending, starttime, endtime)
    	if ($is_blackout) {
            //$url = "index.php?option=com_jongman&tmpl=component&task=reservation.edit&type=$ts&resourceid=$id&start_date=" + start_date + "&reservationid=" + reservationid + '&scheduleid=' + scheduleid + "&is_blackout=" + is_blackout + "&read_only=" + read_only + "&pending=" + pending + "&starttime=" + starttime + "&endtime=" + endtime;     
    		//echo JHTML::link("javascript:reserve('r','$id','$ts','','$scheduleid','1','0','$pending');", $name);
            echo JHTML::link( $url, $name, $attribs );
    	}
    	else {
  	  		// If the user is allowed to make reservations on this resource
        	// then provide a link
        	// Else do not
        	if ($clickable)
                //$url = "index.php?option=com_jongman&tmpl=component&task=reservation.edit&type=$ts&resourceid=$id&start_date=" + start_date + "&reservationid=" + reservationid + '&scheduleid=' + scheduleid + "&is_blackout=" + is_blackout + "&read_only=" + read_only + "&pending=" + pending + "&starttime=" + starttime + "&endtime=" + endtime;     
            	//echo JHTML::link("javascript:reserve('r','$id','$ts','','$scheduleid','0','$pending');", $name);
                echo JHtml::link($url, $name, $attribs);
        	else
            	echo '<span class="inact">' . $name . '</span>';
    	}
    	// Close cell
    	echo "</td>";
	}

	/**
	* Prints out blank columns
	* @param int $cols number of columns to print out
	* @param int $start starting time of the first column printed out
	* @param int $span time span of the schedule
	* @param int $ts timestamp for the reservation start date
	* @param int $resourceid id of the resource on this table row
	* @param int $scheduleid id of the current schedule
	* @param int $scheduleType type of the current schedule
	* @param bool $clickable if this row can be clicked
	* @param string $class class of column background
	*/
	function print_blank_cols($cols, $start, $span, $ts, $resourceid, $scheduleid, $scheduleType, $clickable, $class = '') {
    	$is_blackout = (bool)($scheduleType == BLACKOUT_ONLY);
		$offClass = $clickable ? 'onmouseover="className=\'clickover\'" onmouseout="className=\'clickout\'"' : 'class="o"';

    	$js = '';
    	$url = '';
    	for ($i = 0; $i <= $cols; $i++) {
        	if ($scheduleType != READ_ONLY && ($clickable || $is_blackout)) {
            	$tstart = $start + ($i * $span);
            	$tend = $tstart + $span;
            	
               	$segments = array("option=com_jongman",
               					"task=reservation.add", "layout=modal", "resource_id=$resourceid",
               					"schedule_id=$scheduleid", "ts=$ts", "is_blackout=$is_blackout",
               					"tstart=$tstart", "tend=$tend", "type=$scheduleType",
               					"tmpl=component"
               				);
               	
                $url = JRoute::_("index.php?" . implode('&', $segments) );
        		$options = "{
        				handler:'iframe',
        				size:{x:900,y:500},
        				onClose: function(){
        					if (forceReload) location.reload(true);
        				},  
        		}";
        		
                $js = "onclick=\"javascript:SqueezeBox.fromElement('$url', $options)\"";
        	}
        	echo "<td $offClass $js></td>";
    	}
	}

	/**
	* Prints the closing tr tag
	* @param none
	*/
	function print_closing_tr() {
    	echo "</tr>";
	}

	
	/**
	* Writes out the reservation cell
	* @param int $colspan column span of this reservation
	* @param string $color_select array identifier for which color to use
	* @param string $mod_view indentifying character for javascript reserve function to mod or view reservation
	* @param string $resid id of this reservation
	* @param Summary $summary summary for this reservation
	* @param string $viewable whether the user can click on this reservation and bring up a details box
	* @param int $read_only whether this is a read only schedule
	* @param boolean $pending is this reservation pending approval
	*/

	function writeReservation($colspan, $color_select, $mod_view, $resid, $summary = '', $viewable = false, $read_only = false, $pending = 0) {
	    $params = JComponentHelper::getParams( 'com_jongman' );
	
		$menuitemid = JRequest::getInt( 'Itemid' );
		$menu = JSite::getMenu();
		if ($menuitemid) {
			$menuparams = $menu->getParams( $menuitemid );
			$params->merge( $menuparams );
		}
    	
        $disableReservationSummary = $params->get('disableSummary', 0);
    	$tipClass = $params->get('customTooltip')?'hasCustomTip':'hasTip';
        $js = '';
    	$color = $params->get($color_select . '_colour');
    	$hover = '#' . $params->get($color_select . '_hover');
    	$text  = '#' . $params->get($color_select . '_text' );
        
        
    	$chars = ($colspan > 1) ? 4 * $colspan : 0;

    	$read_only = intval($read_only);

        $summary_text = $summary->toScheduleCell();
	
		$cellSummary = '';
        $tipText = $summary->toScheduleHover();
    	
        if ($viewable) {
            if ($read_only){
                $url = 'index.php?option=com_jongman&task=reservation.view&tmpl=component&layout=readonly&id='.$resid;
            }else{
                $url = 'index.php?option=com_jongman&task=reservation.edit&tmpl=component&layout=modal&id='.$resid;
            }
                
            $url = JRoute::_($url);
            $options = "{
            			handler:'iframe', 
            			size:{x:800,y:500},
            			onClose: function() {
            				if (forceReload) location.reload(true); 
            			}
        			}";
            $js = "onclick=\"javascript:SqueezeBox.fromElement('$url', $options)\"";
        	if ($summary->isVisible()) {
                
                if ($summary_text != $summary->EMPTY_SUMMARY)
                {
                    if (!$disableReservationSummary) 
                    {
                        $cellSummary = "<div class=\"inlineSummary $tipClass\" title=\"$tipText\" style=\"color:$text;background-color:$color;\">$summary_text</div>";
                    }else{
                        $cellSummary = "<div class=\"inlineSummary $tipClass\" title=\"$tipText\" style=\"color:$text;background-color:$color;\">&nbsp;</div>";    
                    }
                }
			}
    	}
    	else {
    	   if ($summary->isVisible()) 
           {
                if (!$disableScheduleSummary) 
                {
       	            $cellSummary = "<div class=\"inlineSummary $tipClass\" title=\"$tipText\" style=\"color:$text;background-color:$color;\">$summary_text</div>";                    
                }else{
               	    $cellSummary = "<div class=\"inlineSummary $tipClass\" title=\"$tipText\" style=\"color:$text;background-color:$color;\">&nbsp;</div>";
                }

			}
    	}
    	echo "<td colspan=\"$colspan\" style=\"color:$text;background-color:$color;\" $js>$cellSummary</td>";
	}
	/**
	* Writes out the blackout cell
	* @param int $colspan column span of the blackout
	* @param bool $edit if the user can edit it
	* @param string $blackoutid id of this blackout
	* @param string $summary blackout summary text
	* @param int $showsummary whether to show the summary or not
	*/

	function write_blackout($colspan, $viewable, $blackoutid, $summary = '', $showsummary = 0) {
    	$params = & JComponentHelper::getParams('com_jongman');
        $tipClass = $params->get('customTooltip')?'hasCustomTip':'hasTip';
    	$color = '#' . $params->get('blackout_color');
    	$hover = '#' . $params->get('blackout_hover');
    	$text  = '#' . $params->get('blackout_text');
    	$chars = 4 * $colspan;
    	$js = '';

        $summary_text = $summary->toScheduleCell();
    	if ($viewable) {
            $url = 'index.php?option=com_jongman&task=reservation.edit&type=m&tmpl=component&id='.$blackoutid;
            
        	if ($showsummary && $summary->isVisible())
			{
				$options = "{handler:'iframe', size:{x:800,y:500},
					onClose: function() {
						if (forceReload) location.reload(true);
					}
				}";
                $js = "onclick=\"javascript:SqueezeBox.fromElement('$url', $options)\"";
                $cellSummary = "<div class=\"inlineSummary $tipClass \" title=\"$tipText\" style=\"color:$text;background-color:$color;\">$summary_text</div>";
			}
    	}
    	else {
    	   if ($showsummary != 0 && $summary->isVisible()){
    	       $url = 'index.php?option=com_jongman&task=reservation.edit&type=m&tmpl=component&id='.$blackoutid;
    	   	   $options = "{handler:'iframe', size:{x:800,y:500},
    	   	   		onClose: function() {
    	   	   			if (forceReload) location.reload(true);
    	   	   			}		
    	   	   	}";
    	   
               $js = "onclick=\"javascript:SqueezeBox.fromElement('$url', $options)\"";
               if ($summary_text != $summary->EMPTY_SUMMARY)
               {
                    $cellSummary = "<div class=\"inlineSummary\" style=\"color:$text;background-color:$color;\">$summary_text</div>";
               }
        	}
            	
    	}

    	echo "<td colspan=\"$colspan\" style=\"color:$text;background-color:$color;\" $js>$cellSummary</td>";
	}

    /**
     * Resturn HTML select list for start/ stop hour selection.
     * @param string $tag_name tag name of HTML select list
     * @param integer $tstart minimum minute of schedule
     * @param integer $tend maximum minute of schedule
     * @param integer $time span of schedule
     * @param integer $selected selected minute
     * @param string $attrib attribute of select list  
     */
    function getHourSelectList($tag_name, $tstart, $tend, $tspan, $selected, $attrib='')
    {
        $html='';
        $options = array();
        for ($i = $tstart; $i < $tend+$tspan; $i += $tspan)
        {
            $options[] = JHtml::_('select.option', $i, DateUtil::formatTime($i));
        }
        //$arr, $name, $attribs = null, $key = 'value', $text = 'text', $selected = NULL, $idtag = false, $translate = false
        $html = JHtml::_('select.genericlist', $options, $tag_name, $attrib, 'value', 'text', $selected );
        
        return $html;    
    }
    
    
    /**
    * Print links to jump to new dates
    * This function prints out the HTML links to allow users to navigate back/forward one week.
    * It also prints the form for users to jump to any given week.
    * @param object schedule to print jump link for 
    */
    function printJumpLinks( $schedule, $scheduleType, $dateVars ) {
        $_date = $dateVars['firstDayTs'];
        $viewdays = $schedule->view_days;
        $printAllCols = ($schedule->view_days!=7);

        if ($scheduleType==BLACKOUT_ONLY) {
            $url = 'index.php?option=com_jongman&task=schedule.display&id='.$schedule->id.'&type='.$scheduleType;     
        }else{
            $url = JSite::getMenu()->getActive()->link;    
        }
        $date = getdate($_date);
        $m = ($date['mon']<10)?'0'.$date['mon']:$date['mon'];
        $d = ($date['mday']<10)?'0'.$date['mday']:$date['mday'];
        $y = $date['year'];
        
        $dateValue = $y."-".$m."-".$d;
        
		$attributes = array('class' => 'inputbox', 
						'title'=>'Jump to Date',
						'size'=>10,
						'maxlength'=>10, 
						'onchange'=>'this.form.submit()');
?>
		<div class="jm-containner">
        	<form name="jumpForm" id="jumpForm" action="<?php echo JRoute::_($url, false)?>" method="post">
                    <?php echo JText::_("COM_JONGMAN_JUMP_TO_DATE")?>
                    <?php echo JHtml::_('calendar', $dateValue, 'jump_date', 'jump_date', '%Y-%m-%d', $attributes);?>
            </form>
        </div>
    </tr>
</table>

<?php
    }
    
    
   	/**
	* Returns an array of all timestamps for repeat reservations
	* @param string $initial_ts timestamp of first reservation
	* @param string $interval interval of reservation recurrances
	* @param array $days days of week to repeat on
	* @param string $until final date of recurrance (IsoDate Format)
	* @param int $frequency frequency of interval
	* @param string $week_number week of month number (for reserve by day of month)
	* @return array of all timestamps that the reservation is repeated on
	*/
	function get_repeat_dates($initial_ts, $interval, $days, $until, $frequency, $week_number) {
		$res_dates = array();
		$initial_date = getdate($initial_ts);
		
		list($last_y, $last_m, $last_d) = explode('-', $until);
		$last_ts = mktime(0,0,0,$last_m, $last_d, $last_y);
		$last_date = getdate($last_ts);
		
		$day_of_week = $initial_date['wday'];
		$day_of_month = $initial_date['mday'];
		
		$ts = $initial_ts;
		
		if ($initial_ts > $last_ts)		// Recurring date is in the past
			return array($ts);
		
		switch ($interval) {
			case 'day' :
				for ($i = $frequency; $ts <= $last_ts; $i += $frequency) {
					$res_dates[] = $ts;
					$ts = mktime(0,0,0, $initial_date['mon'], $i + $initial_date['mday'], $initial_date['year']);						
				}
			break;
			case 'week' :
				$additional_days = 0;
				$res_dates[] = $ts;		// Add initial reservation
				
				while ($ts <= $last_ts) {		
					for ($i = 0; $i < count($days); $i++) {					// Repeat for all days selected
						$days_between = ($days[$i] - $day_of_week) + $additional_days;
						// If the day of week is less than reservation day of week, move ahead one week
						if ($days[$i] <= $day_of_week) {
							$days_between += $frequency * 7;
						}
						$ts = mktime(0,0,0,$initial_date['mon'], $initial_date['mday'] + $days_between, $initial_date['year']);
						
						if ($ts <= $last_ts)
							$res_dates[] = $ts;
					}
					$additional_days += $frequency * 7;	// Move ahead week
				}
			break;
			case 'month_date' :
				$next_month = $initial_date['mon'];
				$res_dates[] = $ts;			// Add initial reservation
				
				while ($ts <= $last_ts) {			
					$next_month += $frequency;
					if (date('t',mktime(0,0,0, $next_month, 1, $initial_date['year'])) >= $initial_date['mday']) {		// Make sure month has enough days
						$ts = mktime(0,0,0,$next_month, $initial_date['mday'], $initial_date['year']);
						if ($ts <= $last_ts)
							$res_dates[] = $ts;
					}
				}
			break;
			case 'month_day' :
				$res_dates[] = $ts;		// Add initial reservation
			
				$days_in_month = date('t', mktime(0,0,0, $initial_date['mon'], $initial_date['mday'], $initial_date['year']));
				$next_month = $initial_date['mon'];
				
				// Fill in all months			
				while ($ts <= $last_ts) {
					
					$days_in_month = date('t', mktime(0,0,0, $next_month, 1, $initial_date['year']));
					$first_day_of_month = date('w', mktime(0,0,0, $next_month, 1, $initial_date['year']));
					$last_day_of_month = date('w', mktime(0,0,0, $next_month, $days_in_month, $initial_date['year']));	
				
					if ($week_number != 'last') {
						$offset_date = ($week_number - 1) * 7 + 1; 		// Starting date
						$day_of_week = $first_day_of_month;				// Day of week
					}
					else {
						$offset_date = $days_in_month - 6;
						$day_of_week = $last_day_of_month + 1;
					}
					
					// Repeat on chosen days for this week
					for ($i = 0; $i < count($days); $i++) {					// Repeat for all days selected
						$days_between = ($days[$i] - $day_of_week);
						
						// If the day of week is less than reservation day of week, move ahead one week
						if ($days[$i] < $day_of_week) {
							$days_between += 7;
						}
						
						$current_date = $offset_date + $days_between;
						
						$need_to_add = ( ($current_date <= $days_in_month) && ($next_month > $initial_date['mon'] || ($current_date >= $initial_date['mday'] && $next_month >= $initial_date['mon'])) );
						
						if ($need_to_add)
							$ts = mktime(0,0,0, $next_month, $current_date, $initial_date['year']);
						
						if ( $ts <= $last_ts && $need_to_add && $ts != $initial_ts)// && ($current_date <= $days_in_month) && ($current_date >= $initial_date['mday'] && $next_month >= $initial_date['mon']) )
							$res_dates[] = $ts;
					}
						
					$next_month += $frequency;
				}	
			break;
		}
		return $res_dates;
	}
    
   	/**
	* Get all reservation data
	* This function gets all reservation data
	* between a given start and end date
	* @param int $start_date the starting date to get reservations for
	* @param int $end_date the ending date to get reservations for
	* @param array $resourceids list of resource ids to get reservations for
	* @param string $current_memberid the id of the currently logged in user
	* @return array of reservation data formatted: $array[date|machid][#] = array of data
	*  or an empty array
	*/
	function getReservations($start_date, $end_date, $resourceids, $schedule_type = RESERVATION_ONLY, $user_id = null) {
		if (is_array($resourceids)) $resource_ids = join(',', $resourceids);
        
		$table_login = '#__users';
		$dbo = & JFactory::getDBO();
        // If it starts between the 2 dates, ends between the 2 dates, or surrounds the 2 dates, get it
		$sql = "SELECT res.*, login.name as name "
		          . " FROM #__jongman_reservations as res"
                  . " LEFT JOIN ".$table_login." as login ON res.reserved_for = login.id "
                  . "\nWHERE ( "
						. "( "
							. "(start_date >= $start_date AND start_date <= $end_date)"
							. " OR "
							. "(end_date >= $start_date AND end_date <= $end_date)"
						. " )"
						. " OR "
						. "(start_date <= $start_date  AND end_date >= $end_date)"
                    . " )";

		if ($schedule_type == "RESERVATION_ONLY")
			$sql .= ' AND res.is_blackout <> 1 ';
		
		$sql .= ' AND res.resource_id IN (' . $resource_ids . ')';
		
		$sql .= "\n ORDER BY res.start_date, res.start_time, res.end_date, res.end_time";
        
		
		$dbo->setQuery($sql);
        
        $rows = $dbo->loadObjectList();
        if (JError::isError($rows) || empty($rows) ) return array();
        $return = array();
		foreach ($rows as $row) {
			$index = $row->resource_id;
			$return[$index][] = $row;
		}
        
        return $return;
	}
    
    function getScheduleFromMenuItem($Itemid = null)
    {
        $menuitemid = ($Itemid?$Itemid:JRequest::getInt( 'Itemid' ));
        if ($menuitemid)
        {
            $link = & JSite::getMenu()->getActive()->link;
            $parts = split('&', $link);
            foreach ($parts as $str)
            {
                list($name, $value) = split('=', $str);
                if ($name=='id')
                {
                    $id = (int)$value;
                    break;
                }
            }
            
            return $id; 
        }
        
        return null;    
    }
    
    function getVersion()
    {
    	$version = stdClass();
    	$manifest = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.'/jongman.xml' ); 
    	$version->longText = 'JONGman '.$manifest->version;
        $version->shortText = $manifest->version;
        return $version;
    }
}