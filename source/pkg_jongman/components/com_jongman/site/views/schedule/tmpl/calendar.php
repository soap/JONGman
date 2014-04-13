<?php
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
jimport('jongman.date.date');
jimport('jongman.application.displayslotfactory');

$Itemid = JFactory::getApplication()->input->getInt('Itemid', null);
$reservationUrl = "index.php?option=com_jongman&task=instance.edit&cid[]=[REFERENCENUMBER]&Itemid=".$Itemid;

$headerDateFormat = JComponentHelper::getParams('com_jongman')->get('headerDateFormat','Y-m-d');
$bottomNavigation = JComponentHelper::getParams('com_jongman')->get('bottomNavigation', false);
$calLink = JURI::root().'media/com_jongman/jongman/images/calendar.png';

$firstDate = $this->scheduledates->getBegin();
$lastDate = $this->scheduledates->getEnd();
$url = JSite::getMenu()->getActive()->link.'&Itemid='.JSite::getMenu()->getActive()->id;
?>
<div class="row-flulid">
	<div clas="span12">
	<div class="schedule_title">
		<span><?php echo $this->schedule->name?></span>
		<a href="#" id="calendar_toggle"><img src="<?php echo $calLink?>" alt="<?php echo JText::_('COM_JONGMAN_SHOWHIDE_CALENDAR')?>"/></a>
	</div>
	<div class="schedule_dates">
		<a href="<?php echo $this->navLinks->previousLink;?>">
		<?php echo JHtml::image('com_jongman/arrow_large_left.png', 'Back', array(), true);?>
		</a>
		<?php echo $firstDate->format($headerDateFormat)?> - <?php echo $lastDate->format($headerDateFormat)?>
		<a href="<?php echo $this->navLinks->nextLink;?>">	
		<?php echo JHtml::image('com_jongman/arrow_large_right.png', 'Forward', array(), true);?>
		</a>	
	</div>
	<div type="text" id="datepicker" style="display:none"></div>

<?php 
echo $this->loadTemplate('legend');
echo $this->loadTemplate('main');
if ($bottomNavigation) {
	echo $this->loadTemplate('footer');
}
?>
	</div>
</div>
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