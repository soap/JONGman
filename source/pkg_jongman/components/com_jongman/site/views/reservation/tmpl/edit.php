<?php
//JHtml::addIncludePath(JPATH_COMPONENT.'helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('reservation-form');
		if (task == 'reservation.cancel' || document.formvalidator.isValid(form)) {
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
<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="reservation-form" class="form-validate form-inline">
	<div class="row-fluid">
		<div class="span-12">
			<div class="formelm-buttons btn-toolbar pull-right">
				<button type="button" class="btn btn-info"
					onclick="Joomla.submitbutton('reservation.save')">
					<?php echo JText::_('JSAVE') ?>
				</button>
				<button type="button" class="btn btn-info"
					onclick="Joomla.submitbutton('reservation.cancel')">
					<?php echo JText::_('JCANCEL') ?>
				</button>
			</div>
			<div class="pull-left">
				<legend>
				<?php echo JText::_('COM_JONGMAN_RESERVATION_TITLE')?>
				</legend>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span6 form-horizontal">
			<div class="control-group">
				<div class="control-label">
				<?php echo $this->form->getLabel('title'); ?>
				</div>
				<div class="controls">
				<?php echo $this->form->getInput('title'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('reference_number'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('reference_number'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('owner_id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('owner_id'); ?>
				</div>
			</div>
						
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('resource_id'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('resource_id'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('start_date'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('start_date'); ?>
					<?php echo $this->form->getInput('start_time'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('end_date'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('end_date'); ?>
					<?php echo $this->form->getInput('end_time'); ?>
				</div>
			</div>
			<div class="control-grouop">
				<div class="control-label">
					<?php echo $this->form->getLabel('description'); ?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('description'); ?>
				</div>
			</div>
		</div>
		<div class="span6">
		</div>
	</div>
	<?php echo $this->form->getInput('created_by')?>
	<?php echo $this->form->getInput('schedule_id')?>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="return" value="<?php echo base64_encode($this->state->get('return_page')); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>