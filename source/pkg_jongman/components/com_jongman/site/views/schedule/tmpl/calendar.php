<?php
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
jimport('jongman.date.date');
jimport('jongman.application.displayslotfactory');

$return = base64_encode(JFactory::getURI()->toString());
$Itemid = JFactory::getApplication()->input->getInt('Itemid', null);
$reservationUrl = "index.php?option=com_jongman&task=instance.edit&cid[]=[REFERENCENUMBER]&Itemid=".$Itemid;

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
		<span><?php echo $this->schedule->name?></span>
		<a href="#" id="calendar_toggle"><img src="<?php echo $calLink?>" alt="<?php echo JText::_('COM_JONGMAN_SHOWHIDE_CALENDAR')?>"/></a>
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
<form name="reservationForm" id="reservation-form" method="POST" action="<?php JFactory::getURI()->toString()?>">
	<input type="hidden" name="cid[]" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo base64_encode(JFactory::getURI()->toString());?>" />
</form>
<script type="text/javascript">
	var forceReload = false;
	window.location.hash = 'sc-top';

	window.addEvent('domready', function() {
		var scheduleOpts = {
			reservationUrlTemplate: '<?php echo $reservationUrl?>',
			summaryPopupUrl: "index.php?option=com_jongman&tmpl=component&view=instanceitem",
			cookieName: "schedule-direction-1",
			cookieValue: "horizontal"
		};

		var schedule = new Schedule(scheduleOpts);
		schedule.init();

		jQuery('#datepicker').datepicker({
			numberOfMonths: 3,
			showButtonPanel: true,
			onSelect: dpDateChanged,
			dateFormat: 'yy-mm-dd',
			firstDay: <?php echo $this->schedule->weekday_start?>,
			currentText: '<?php echo JText::_('COM_JONGMAN_TODAY')?>'
			
		});
	});

	
</script>