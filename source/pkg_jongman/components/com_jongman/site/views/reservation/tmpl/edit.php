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
			<?php //echo $this->form->getField('description')->save(); ?>
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
<div class="edit">
<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="reservation-form" class="form-validate form-inline">
	<div>
	<div class="formelm-buttons btn-toolbar">
		<button type="button" class="btn btn-info" onclick="Joomla.submitbutton('reservation.save')">
			<?php echo JText::_('JSAVE') ?>
		</button>
		<button type="button" class="btn btn-info" onclick="Joomla.submitbutton('reservation.cancel')">
			<?php echo JText::_('JCANCEL') ?>
		</button>
	</div>
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_JONGMAN_RESERVATION_TITLE')?></legend>
    		<div class="formelm control-group">
                <div class="control-label">
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</div>
			</div>

    		<div class="formelm control-group">
                <div class="control-label">
					<?php echo $this->form->getLabel('alias'); ?>
					<?php echo $this->form->getInput('alias'); ?>
				</div>
			</div>
    		<div class="formelm control-group">
                <div class="control-label">
					<?php echo $this->form->getLabel('resource_id'); ?>
					<?php echo $this->form->getInput('resource_id'); ?>
				</div>
			</div>			
    		<div class="formelm control-group">
                <div class="control-label">
					<?php echo $this->form->getLabel('start_date'); ?>
					<?php echo $this->form->getInput('start_date'); ?>
				</li>				
    		<div class="formelm control-group">
                <div class="control-label">
					<?php echo $this->form->getLabel('end_date'); ?>
					<?php echo $this->form->getInput('end_date'); ?>
				</div>
			</div>				


			<?php echo $this->form->getLabel('description'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('description'); ?>

		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<?php //echo JHtml::_('sliders.start','reservation-sliders-'.$this->item->id, array('useCookie' => 1)); ?>

		<?php //echo $this->loadTemplate('params'); ?>

		<?php //echo JHtml::_('sliders.end'); ?>

	</div>
	<div class="clr"></div>
	<input type="hidden" name="schedule_id" value="<?php echo $this->item->schedule_id?>>" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
</div>