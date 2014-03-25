<?php
defined('_JEXEC') or die;
JText::script("COM_JONGMAN_HOURS_1");
JText::script("COM_JONGMAN_HOURS_MORE");
JText::script("COM_JONGMAN_DAYS_1");
JText::script("COM_JONGMAN_DAYS_MORE");
JText::script("COM_JONGMAN_WEEKS_1");
JText::script("COM_JONGMAN_WEEKS_MORE");
JText::script("COM_JONGMAN_MONTHS_1");
JText::script("COM_JONGMAN_MONTHS_MORE");
JText::script("COM_JONGMAN_YEARS_1");
JText::script("COM_JONGMAN_YEARS_MORE");
?>

<div class="control-group" id="repeatOptions">
	<div class="control-label">
		<?php echo $this->form->getLabel('repeat_type'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('repeat_type'); ?>
	</div>
</div>
<div id="repeatDiv">
	<div class="control-group days weeks months years" id="repeatInterval">
		<div class="control-label">
		<?php echo $this->form->getLabel('repeat_interval'); ?>
		</div>
		<div class="controls">
		<?php echo $this->form->getInput('repeat_interval'); ?>
		</div>
	</div>

	<div class="control-group weeks" id="repeatOnlyWeek">
		<div class="control-label">
			<?php echo $this->form->getLabel('repeat_days'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('repeat_days'); ?>
		</div>
	</div>

	<div class="control-group months">
		<div class="control-label">
			<?php echo $this->form->getLabel('repeat_monthly_type'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('repeat_monthly_type'); ?>
		</div>
	</div>

	<div class="control-group days weeks months years" id="repeatUntilDiv">
		<div class="control-label">
			<?php echo $this->form->getLabel('repeat_terminated'); ?>
		</div>
		<div class="controls">
			<?php echo $this->form->getInput('repeat_terminated'); ?>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {
	var resOpts = {
		layoutUrl: '<?php echo JRoute::_('index.php?option=com_jongman&task=schedule.layout&format=json')?>',
	};
	var reservation = new Reservation(resOpts);
	reservation.init();
	
	var recurOpts = {
        repeatType:'<?php echo $this->item->repeat_type?>',
        repeatInterval:'<?php echo $this->item->repeat_options->get('repeat_interval');?>',
        repeatMonthlyType:'<?php echo $this->item->repeat_options->get('repeat_monthly_type');?>',
        repeatWeekdays:'<?php echo $this->item->repeat_options->get('repeat_days');?>'
    };
    var recurrence = new Recurrence(recurOpts);
    recurrence.init();
});
</script>

