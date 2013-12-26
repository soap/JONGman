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

<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=modal&tmpl=component&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="timeslot-form" class="form-validate">
	<div class="width-40 fltlft" id="layoutDialog-left">
		<h5>Reservable Slots</h5>
		<textarea name="reservableEdit" id="reservableEdit" rows="15" cols="20" style="margin: 0px; display: inline-block; text-indent: 0px; text-align: start;">
		</textarea>
	</div>
	<div class="width-50 fltrt" id="layoutDialog-right">
		<h5>Blocked Slots</h5>
		<textarea name="blockedEdit" id="blockedEdit" rows="15" cols="20" style="margin: 0px; display: inline-block; text-indent: 0px; text-align: start;">
		</textarea>
	</div>
	<div class="clr"></div>

	<?php echo JText::_('COM_JONGMAN_CREATE_TIMESLOT_EVEREY')?>
	<input type="text" name="interval" id="interval" value="30" size="4" maxlength="4" />
	<?php echo JText::_('COM_JONGMAN_MINUTES')?>
	<?php echo JText::_('COM_JONGMAN_FROM')?>
	<input type="text" name="start_time" id="start_time" value="08:00" size="5"/>
	<?php echo JText::_('COM_JONGMAN_UNTIL')?>
	<input type="text" name="enf_time" id="end_time" value="17:00" size="5"/>
	
	<button type="submit" class="button2 right" onclick="javascript:genTimeSlot();"><?php echo JText::_('COM_JONGMAN_CREATE')?></button>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
	function genTimeSlot() {
		alert('aaaa');
		
	}	
</script>