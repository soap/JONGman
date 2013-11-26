<?php
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
jimport('jongman.date.date');
jimport('jongman.application.displayslotfactory');


$first_date = date("Y-m-d H:i:s", $this->datevars['firstDayTs']);
$last_date = date("Y-m-d H:i:s", $this->datevars['lastDayTs']);

$headerDateFormat = JComponentHelper::getParams('com_jongman')->get('headerDateFormat');
$topNavigation = JComponentHelper::getParams('com_jongman')->get('topNavigation');
$calLink = JURI::root().'media/com_jongman/jongman/images/calendar.png';

$firstDate = $this->scheduledates->getBegin();
$lastDate = $this->scheduledates->getEnd();
?>
<div>
	<div class="schedule_title">
		<span><?php echo $this->schedule->name?><a href="#" id="calendar-toggle"><img src="<?php echo $calLink?>"/></a></span>
	</div>
	<div class="schedule_dates">
		<?php echo $firstDate->format($headerDateFormat)?> - <?php echo $lastDate->format($headerDateFormat)?>	
	</div>
	<div type="text" id="datepicker" style="display:none"></div>
</div>
<?php 
echo $this->loadTemplate('legend');
if ($topNavigation) :
	echo $this->loadTemplate('footer');
endif;
echo $this->loadTemplate('main');
echo $this->loadTemplate('footer');
?>
<div class="jm-containner">
	<?php echo JongmanHelper::printJumpLinks($this->schedule, $this->state->get('scheduleType'), $this->datevars)?>
</div>

<script type="text/javascript">
	var forceReload = false;
	window.location.hash = 'sc-top';

	window.addEvent('domready', function() {
		var scheduleOpts = {
			reservationUrlTemplate: "index.php?option=com_jongman&task=reservation.edit&alias=[referenceNumber]",
			summaryPopupUrl: "index.php?option=com_jongman&format=json&task=reservation.view",
			cookieName: "schedule-direction-1",
			cookieValue: "horizontal"
		};

		var schedule = new Schedule(scheduleOpts);
		schedule.init();
	});
</script>