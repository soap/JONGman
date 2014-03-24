<?php
defined('_JEXEC') or die;

?>
<div class="control-group">
	<div class="control-label">
		<?php echo $this->form->getLabel('repeat_type'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('repeat_type'); ?>
	</div>
</div>

<div class="control-group repeatoption">
	<div class="control-label">
		<?php echo $this->form->getLabel('repeat_interval'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('repeat_interval'); ?>
	</div>
</div>

<div class="control-group  repeatoption not-daily not-monthly not-yearly">
	<div class="control-label">
		<?php echo $this->form->getLabel('repeat_days'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('repeat_days'); ?>
	</div>
</div>

<div class="control-group repeatoption not-daily not-weekly not-yearly">
	<div class="control-label">
		<?php echo $this->form->getLabel('repeat_monthly_type'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('repeat_monthly_type'); ?>
	</div>
</div>

<div class="control-group repeatoption">
	<div class="control-label">
		<?php echo $this->form->getLabel('repeat_terminated'); ?>
	</div>
	<div class="controls">
		<?php echo $this->form->getInput('repeat_terminated'); ?>
	</div>
</div>

<script type="text/javascript">
	jQuery('.repeatoption').hide();
	var recurOpts = {
        repeatType:'{$this->item->repeat_type}',
        repeatInterval:'{$this->item->repeat_interval}',
        repeatMonthlyType:'{$this->item->repeat_monthly_type}',
        repeatWeekdays:[{$this->item->repeat_days}]
    };
</script>

