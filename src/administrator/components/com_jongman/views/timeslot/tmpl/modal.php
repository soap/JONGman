<?php
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('timeslot-form');
		if (task == 'timeslot.cancel' || document.formvalidator.isValid(form)) {
			Joomla.submitform(task, form);
		}
		else {
			<?php JText::script('COM_JONGMAN_ERROR_N_INVALID_FIELDS'); ?>
			// Count the fields that are invalid.
			var elements = form.getElements('fieldset').concat(Array.from(form.elements));
			var invalid = 0;

			for (var i = 0; i < elements.length; i++) {
				if (document.formvalidator.validate(elements[i]) == false) {
					valid = false;
					invalid++;
				}
			}

			alert(Joomla.JText._('COM_JONGMAN_ERROR_N_INVALID_FIELDS').replace('%d', invalid));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=modal&tmpl=component&layout_id='.(int) $this->item->layout_id); ?>"
	method="post" name="adminForm" id="timeslot-form" class="form-validate">
	<div class="form-inline form-inline-header">
		<div class="row-fluid">
			<div class="span8">
			<?php echo $this->form->renderField('timezone')?>
			<?php echo $this->form->renderField('dailylayout')?>
			</div>
			<div class="span4">
				<button type="submit" class="btn btn-small btn-primary" onclick="Joomla.submitbutton('timeslot.save');"><?php echo JText::_('JTOOLBAR_SAVE')?></button>
			</div>
		</div>
	</div>
	<div class="form-vertical">
		<div class="row-fluid">
			<div class="span6" id="layoutDialog-left">
				<?php echo $this->form->renderField('reservable_slots')?>
			</div>
			<div class="span6" id="layoutDialog-right">
				<?php echo $this->form->renderField('blocked_slots')?>
			</div>
		</div>
	</div>
	<div class="form-inline form-inline-header">
		<?php echo JText::_('COM_JONGMAN_CREATE_TIMESLOT_EVEREY')?>
		<div class="input-append">
			<input type="text" name="interval" id="quickLayoutInterval" value="30" class="input-small" />
			<span class="add-on"><?php echo JText::_('COM_JONGMAN_MINUTES')?></span>
		</div>
		<?php echo JText::_('COM_JONGMAN_FROM')?>
		<input type="text" name="start_time" id="quickLayoutStart" value="08:00" class="input-small"/>
		<?php echo JText::_('COM_JONGMAN_UNTIL')?>
		<input type="text" name="enf_time" id="quickLayoutEnd" value="17:00" class="input-small"/>
		<button type="button" class="btn btn-small btn-primary" id="createQuickLayout"><?php echo JText::_('COM_JONGMAN_CREATE')?></button>
	</div>
	<?php echo $this->form->getInput('layout_id')?>
	<input type="hidden" name="layout_id" value="<?php echo $this->item->layout_id?>" />
	<input type="hidden" name="task" value="" />	
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
	jQuery(document).ready(function() {
		var scheduleManager = new ScheduleManagement();
		scheduleManager.init();	
	});
</script>