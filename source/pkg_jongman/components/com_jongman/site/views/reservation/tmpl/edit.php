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
		if (task == 'reservation.cancel' || task == 'instance.cancel' || document.formvalidator.isValid(form)) {
			<?php echo $this->form->getField('description')->save() ?>
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
<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=edit&id='.(int) $this->item->instance_id); ?>"
	method="post" name="adminForm" id="reservation-form" class="form-validate form-inline">
	<div class="row-fluid">
		<div class="span12">
			<div class="formelm-buttons btn-toolbar pull-right">
				<?php echo $this->toolbar?>
			</div>
			<?php echo $this->title?>
		</div>
	</div>
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JONGMAN_RESERVARION_DETAILS', true)); ?>
	<div class="row-fluid">	
		<div class="span7 form-horizontal">
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
				<div class="controls form-inline">
					<?php echo $this->form->getInput('start_date'); ?>
					<?php echo $this->form->getInput('start_time'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('end_date'); ?>
				</div>
				<div class="controls form-inline">
					<?php echo $this->form->getInput('end_date'); ?>
					<?php echo $this->form->getInput('end_time'); ?>
				</div>
			</div>
			<div class="control-group">
				<div class="control-label">
					<?php echo $this->form->getLabel('reservation_length')?>
				</div>
				<div class="controls">
					<?php echo $this->form->getInput('reservation_length')?>
				</div>
			</div>
		</div>
		<div class="span5 form-horizontal">
			<?php echo $this->loadTemplate('repeatoptions')?>
		</div>
	</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'description', JText::_('COM_JONGMAN_RESERVATION_DESCRIPTION', true)); ?>
		<div class="control-grouop form-inline">
			<div class="control-label">
				<?php echo $this->form->getLabel('description'); ?>
			</div>
			<div class="controls">
				<?php echo $this->form->getInput('description'); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php if ($this->customFields) : ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'customfields', JText::_('COM_JONGMAN_RESERVATION_CUSTOMFIELD_FIELDSET', true)); ?>
			<div class="row-fluid form-horizontal-desktop">
			<?php echo $this->loadTemplate('customfields'); ?>
			</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>
	<?php echo JHtml::_('bootstrap.endTabSet');?>

	<?php echo $this->form->getInput('schedule_id')?>
	
	<input type="hidden" name="schedule_id" value="<?php echo $this->item->schedule_id?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->instance_id?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>