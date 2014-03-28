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
	<div>
		<button type="submit" class="button2 right" onclick="Joomla.submitbutton('timeslot.save');"><?php echo JText::_('JTOOLBAR_SAVE')?></button>
	</div>
	<div class="clr"></div>
	<div class="width-100 fltlft">
		<fieldset>
			<ul>
				<li>
					<?php echo $this->form->getLabel('timezone')?>
					<?php echo $this->form->getInput('timezone')?>
				</li>
			</ul>
		</fieldset>
	</div>
	<div class="width-40 layoutDialog fltlft" id="layoutDialog-left">
		<fieldset name="static-reservable">
		<ul>
			<li>
				<?php echo $this->form->getLabel('reservable_slots')?>
			</li>
			<li>
				<?php echo $this->form->getInput('reservable_slots')?>
			</li>
		</ul>
		</fieldset>
	</div>
	<div class="width-50 layoutDialog fltrt" id="layoutDialog-right">
		<fieldset name="static-blocked">
		<ul>
			<li>
				<?php echo $this->form->getLabel('blocked_slots')?>
			</li>
			<li>
				<?php echo $this->form->getInput('blocked_slots')?>
			</li>
		</ul>	
		</fieldset>	
	</div>
	<div class="clr"></div>

	<?php echo JText::_('COM_JONGMAN_CREATE_TIMESLOT_EVEREY')?>
	<input type="text" name="interval" id="quickLayoutInterval" value="30" size="4" maxlength="4" />
	<?php echo JText::_('COM_JONGMAN_MINUTES')?>
	<?php echo JText::_('COM_JONGMAN_FROM')?>
	<input type="text" name="start_time" id="quickLayoutStart" value="08:00" size="5"/>
	<?php echo JText::_('COM_JONGMAN_UNTIL')?>
	<input type="text" name="enf_time" id="quickLayoutEnd" value="17:00" size="5"/>
	
	<button type="button" class="button2 right" id="createQuickLayout"><?php echo JText::_('COM_JONGMAN_CREATE')?></button>
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