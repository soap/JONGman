<?php
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
$document = JFactory::getDocument();
$document->addStyleSheet( JURI::root(true).'/media/com_jongman/css/mooGrowl.css');
$document->addScript(JURI::root(true).'/media/com_jongman/js/mooGrowl.js');
$document->addScript(JURI::root(true).'/media/com_jongman/js/validate.js');
?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('reservation-form');
		if (task == 'reservation.cancel' || document.formvalidator.isValid(form)) {
			<?php echo $this->form->getField('description')->save(); ?>
			if (task == 'reservation.save') {
				
				var id = document.getElementById('jform_id').value;
				var resource_id = document.getElementById('jform_resource_id').value;
				var start_date = document.getElementById('jform_start_date').value;
				var end_date = document.getElementById('jform_end_date').value;
				var start_time = document.getElementById('jform_start_time').value;
				var end_time = document.getElementById('jform_end_time').value;

				Joomla.submitform(task, form);
			}else{
				Joomla.submitform(task, form);
			}
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

<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=modal&id='.(int) $this->item->id); ?>"
	method="post" name="reservation-form" id="reservation-form" class="adminform form-validate">
	<fieldset id="jm-fs-reservation">
		<div class="fltrt">
			<?php echo $this->toolbar?>
		</div>
		<div class="configuration" >
			<?php echo $this->title ?>
		</div>
	</fieldset>
	<?php 
	echo JHtml::_('tabs.start', 'jm-tabs-reservation', array('offset'=>0, 'useCookie'=>false));
	
	echo $this->loadTemplate('general');
	
	echo $this->loadTemplate('period');
	
	echo $this->loadTemplate('repeat');
	?>
	<div class="clr"></div>
	<?php echo JHtml::_('tabs.end'); ?>
	
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="popup" value="1" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
	var el = document.getElementById('jform_repeat_interval')
	if (el.value=='none') 
	{
		changeInterval(el);	
	}	
</script>

