<?php
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');

$first_date = date("Y-m-d H:i:s", $this->datevars['firstDayTs']);
$last_date = date("Y-m-d H:i:s", $this->datevars['lastDayTs']);

$headerDateFormat = JComponentHelper::getParams('com_jongman')->get('headerDateFormat');
$topNavigation = JComponentHelper::getParams('com_jongman')->get('topNavigation');
?>
<div class="jm_title">
	<a hrer="#" id="sc-top">&nbsp;</a>
	<h3 style="text-align:center">
		<?php echo JongmanHelper::getScheduleHeader($this->schedule->name, $first_date, $last_date, $headerDateFormat, ($this->schedule->view_days != 1))?>
	</h3>
</div>
<?php echo $this->loadTemplate('colourkeys');
if ($topNavigation): ?>
<div class="jm-containner">
	<?php echo $this->navigation->getListFooter();?>	 
</div>
<div class="clear-both"></div>
<?php endif;

// Break first day we are viewing into an array of date pieces
$temp_date = getdate($this->datevars['firstDayTs']);
$hour_header = JongmanHelper::getHourHeader($this->timearray, $this->schedule->day_start, $this->schedule->day_end, $this->schedule->time_span); 
// Repeat this for each day we need to show
for ($dayCount = 0; $dayCount < $this->schedule->view_days; $dayCount++) 
{
	// Timestamp for whatever day we are currently viewing
	$this->datevars['current'] = mktime(0,0,0, $temp_date['mon'], $temp_date['mday'] + $dayCount, $temp_date['year']);
	// Start the table for this day
	echo JongmanHelper::getStartDayTable(
		DateUtil::formatReservationDate($this->datevars['current'], $this->schedule->day_start, null), 
		$hour_header, 
		$this->datevars['now'] == $this->datevars['current']);    
    	
	$current_date = $this->datevars['current']; // Store current_date so we dont have to access the array every time

    // Repeat this whole process for each resource in the database (in schedule)
    for ($count = 0; $count < count($this->resources); $count++) 
    {
    	$prevTime = $this->schedule->day_start; // Previous time holder
    	// Total columns holder
    	$totCol = intval(($this->schedule->day_end - $this->schedule->day_start) / $this->schedule->time_span);
		$cur_resource = $this->resources[ $count ];

        // Store info about this current resource in local vars
        $id = $cur_resource->id;
        $name = $cur_resource->title;
        $status = $cur_resource->status; //do we use published?
        $approval = $cur_resource->need_approval;
        
		// Default resource visiblilty to not shown
        $clickable = false; 
        $addable = false;
               
		$viewable_date = $this->isViewableDate($current_date, $cur_resource->min_notice_time, $cur_resource->max_notice_time);
        // If the date has not passed, resource is active and user has permission,
        //  or the user is the admin allow reservations to be made
       	$clickable = $this->canClickReservation($viewable_date, $cur_resource);
		$addable = $this->canAddReservation($viewable_date, $cur_resource);
	
		if ($this->state->get("scheduleType") == READ_ONLY)
		{
			$color_class = 'ro' . ($count%2);
		}
		else
		{
            $color_class = 'r' . ($count%2);
		}
        JongmanHelper::print_name_cell($current_date, $id, $name, $addable, $this->state->get("scheduleType") == BLACKOUT_ONLY, $this->schedule->id, $approval, $color_class);

        $index = $id; //resource id
        if (isset($this->reservations[$index])) 
        {            
        	for ($i = 0; $i < count($this->reservations[$index]); $i++) 
        	{
        		
            	/** FIXED by Prasit Gebsaap: 
                    For PHP5, we have to clone it, otherwise it will overwrite orginal reservation properties **/
                $rs = clone $this->reservations[$index][$i];
				
                // If it doesn't start sometime today, end sometime today, or surround today, just skip over it
                if (
                	!(($rs->start_date >= $current_date && $rs->start_date <= $current_date)
                    || ($rs->end_date >= $current_date && $rs->end_date <= $current_date)
                    || ($rs->start_date <= $current_date && $rs->end_date >= $current_date))
                 	) {
                        continue;
                }
				
                // Just skip the reservation if the ending date/time is todays start time
                if ($rs->end_date == $current_date && $rs->end_time == $this->schedule->day_start) { continue; }

                // If the reservation starts before or ends after todays date, just pretend it ends today so it shows correctly
                if ($rs->start_date < $current_date) {
                	$rs->start_time = $this->schedule->day_start;
                }
                
                if ($rs->end_date > $current_date) {
                	$rs->end_time = $this->schedule->day_end;
                }

                // Print out row of reservations
                $thisStart = $rs->start_time;
                $thisEnd = $rs->end_time;

                if ($thisStart < $this->schedule->day_start && $thisEnd > $this->schedule->day_start)
                	$thisStart = $this->schedule->day_start;
                else if ($thisStart < $this->schedule->day_start && $thisEnd <= $this->schedule->day_start)
                	continue;    // Ignore reservation, its off the schedule
				
                if ($thisStart < $this->schedule->day_end && $thisEnd > $this->schedule->day_end)
                	$thisEnd = $this->schedule->day_end;
                else if ($thisStart >= $this->schedule->day_end && $thisEnd > $this->schedule->day_start)
                	continue;    // Ignore reservation, its off the schedule
                	
                $colspan = intval(($thisEnd - $thisStart) / $this->schedule->time_span);

        		$cols = (($thisStart-$prevTime) / $this->schedule->time_span) - 1;
				JongmanHelper::print_blank_cols($cols, $prevTime, $this->schedule->time_span, 
					$current_date, $id, $this->schedule->id, $this->state->get('scheduleType'), $addable, $color_class);
    			
                if ($rs->is_blackout == 1)
                	$this->writeBlackout($rs, $colspan);
                else
                	$this->writeReservation($rs, $colspan, $viewable_date);
			
                // Set prevTime to this reservation's ending time
                $prevTime = $thisEnd;
        	}
        }
       	/* ================= ending row start ============================= */
    	$cols = (($this->schedule->day_end-$prevTime) / $this->schedule->time_span) - 1;
		
		JongmanHelper::print_blank_cols($cols, $prevTime, $this->schedule->time_span, $current_date, $id, $this->schedule->id, $this->state->get("scheduleType"), $addable, $color_class);
    	JongmanHelper::print_closing_tr();
    	/* ================== ending row end ============================== */
    } //end resources
    
    
    echo JongManHelper::getEndDayTable();  // End the table for this day	
} //end days 
?>
<div class="jm-containner">
	<?php echo $this->navigation->getListFooter();?>
</div>
<div class="jm-containner">
	<?php echo JongmanHelper::printJumpLinks($this->schedule, $this->state->get('scheduleType'), $this->datevars)?>
</div>
<script>
	var forceReload = false;
	window.location.hash = 'sc-top';
</script>