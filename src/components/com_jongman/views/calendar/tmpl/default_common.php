<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
$displayDate = $this->displayDate;
$today = RFDate::now()->toTimezone(RFApplicationHelper::getUserTimezone());
$calendarTimeFormat = 'h:mmt';
$calendarDateFormat = 'd/M';
$Itemid = JFactory::getApplication()->input->getInt('Itemid');
//$url = 'index.php?option=com_jongman&task=instance.view&itemId='.JFactory::getApplication()->input->getInt('Itemid');
$reservable = ((JFactory::getUser()->get('id') > 0) && ( ($this->resourceId > 0) || ($this->scheduleId > 0)) ) ? 'true' : 'false';
?>
<div class="calendar-container">
	<div id="calendar" class=""></div>

	<div id="dayDialog" class="dialog">
		<a href="#" id="dayDialogCreate"><?php echo JHtml::image('com_jongman/jongman/tick.png', 'New Reservation', array(), true);?>New Reservation</a>
		<a href="#" id="dayDialogView"><?php echo JHtml::image('com_jongman/jongman/search.png', 'View', array(), true);?>View Day</a>
		<a href="#" id="dayDialogCancel"><?php echo JHtml::image('com_jongman/jongman/slash.png', 'Cancel', array(), true);?>Cancel</a>
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {

	var aDayNames = [];
	var aDayNamesShort = [];
	var aMonthNames = [];
	var aMonthNamesShort = [];
	var reservations = [];
	<?php 
		if (count($this->calendar->getReservations()) > 0) :		
			foreach ($this->calendar->getReservations() as $reservation) :?>
				reservations.push({
					id: '<?php echo $reservation->referenceNumber?>',
					title: '<?php echo $reservation->displayTitle?>',
					start: '<?php echo $reservation->startDate->format('Y-m-d H:i:s');?>',
					end: '<?php echo $reservation->endDate->format('Y-m-d H:i:s');?>',
					url: '<?php echo JRoute::_('index.php?option=com_jongman&task=instance.view&id='.$reservation->seriesId, false ); ?>',
					allDay: false,
					color: '<?php echo $reservation->color; ?>',
					textColor: '<?php echo $reservation->textColor; ?>',
					className: '<?php echo $reservation->class; ?>'
				});
	<?php 
			endforeach;
		endif;
	?>

	var options = {
		view: '<?php echo $this->viewMode?>',
		year: '<?php echo $displayDate->year()?>',
		month: '<?php echo $displayDate->month()?>',
		date: '<?php echo $displayDate->day()?>',
		dayClickUrl: '<?php echo JRoute::_('index.php?option=com_jongman&view=calendar&caltype=day&sid='.$this->scheduleId.'&rid='.$this->resourceId, false)?>',
		dayNames: aDayNames,
		dayNamesShort: aDayNamesShort,
		monthNames: aMonthNames,
		monthNamesShort: aMonthNamesShort,
		timeFormat: '<?php echo $calendarTimeFormat?>',
		dayMonth: '<?php echo $calendarDateFormat?>',
		firstDay: <?php echo $this->firstDay?>,
		reservationUrl: '<?php echo JRoute::_('index.php?option=com_jongman&task=reservation.add&sid='.$this->scheduleId.'&rid='.$this->resourceId, false)?>',
		reservable: <?php echo $reservable?>
	};

	var calendar = new Calendar(options, reservations);
	calendar.init();
	//calendar.bindResourceGroups({$ResourceGroupsAsJson}, {$SelectedGroupNode|default:0});

	jQuery('#fc-day-btn').click(function(e){
			window.location ='<?php echo RFCalendarUrl::create($today, RFCalendarTypes::Day)?>';
		});
	jQuery('#fc-week-btn').click(function(e){
			window.location ='<?php echo RFCalendarUrl::create($today, RFCalendarTypes::Week)?>';
		});
	jQuery('#fc-month-btn').click(function(e){
			window.location = '<?php echo RFCalendarUrl::create($displayDate, RFCalendarTypes::Month)?>';
		});
	jQuery('.fc-button').hover(
			function() {
				jQuery(this).addClass('hover');
			},
				
			function() {
				jQuery(this).removeClass('hover');
			}
		);
});
</script>