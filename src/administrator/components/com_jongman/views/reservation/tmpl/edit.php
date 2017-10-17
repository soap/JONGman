<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access
defined('_JEXEC') or die;
//JHtml::addIncludePath(JPATH_COMPONENT.'helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//add this translation to javascript strings
JText::script('COM_JONGMAN_FORM_SELECT_RESOURCE');
$link = "index.php?option=com_jongman&view=resources&layout=modal&tmpl=component&field=jform_resource_id&filter_schedule_id=";
?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('reservation-form');
		if (task == 'reservation.cancel' || document.formvalidator.isValid(form)) {
			<?php //echo $this->form->getField('summary')->save(); ?>
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

	// Onchange function for schedule select list
	function scheduleChanged(el) {
		old_el = document.id("old_schedule_id");
		schedule_el = document.id("jform_schedule_id");
		if ( schedule_el.get("value")=="") {
			schedule_el.set("value", old_el.get("value"));
			return;
		}
		if ( old_el.get("value") != schedule_el.get("value") ) {
			old_el.set("value", schedule_el.get("value"));
			document.id("jform_resource_id_id").set("value", "");
			document.id("jform_resource_id_name").set("value", "");
			var button_el = document.id("modal_jform_resource_id");
			var link = '<?php echo $link?>'+schedule_el.get('value');
			button_el.set("html", Joomla.JText._('COM_JONGMAN_FORM_SELECT_RESOURCE'));
			button_el.set("rel", link);
			
		}else{
			//do not thing
		}	
	}

	function startDateChange(el) {
		var start_el = document.id("jform_start_date");
		var end_el = document.id("jform_end_date");
		
		if ((end_el.getProperty("readonly")==true) || (end_el.getProperty("readonly")=="readonly")) {
			end_el.set("value", start_el.get("value"));
			end_el.removeClass("required");
		}	
		return true;	
	}	 	
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="reservation-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
		<legend><?php echo empty($this->item->id) ? JText::_('COM_JONGMAN_NEW_RESERVATION') : JText::sprintf('COM_JONGMAN_EDIT_RESERVATION', $this->item->id); ?></legend>
			<ul class="adminformlist">
				<li>
					<?php echo $this->form->getLabel('id'); ?>
					<?php echo $this->form->getInput('id'); ?>
				</li>
				<li>
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</li>
				<li>
					<?php echo $this->form->getLabel('alias'); ?>
					<?php echo $this->form->getInput('alias'); ?>
				</li>
				<li>
					<?php echo $this->form->getLabel('schedule_id'); ?>
					<?php echo $this->form->getInput('schedule_id'); ?>
				</li>
				<li>
					<?php echo $this->form->getLabel('resource_id'); ?>
					<?php echo $this->form->getInput('resource_id'); ?>
				</li>
				<li>
					<?php echo $this->form->getLabel('reserved_for'); ?>
					<?php echo $this->form->getInput('reserved_for'); ?>					
				</li>
			</ul>
			<div class="clr"></div>
			<?php echo $this->form->getLabel('description'); ?>
			<div class="clr"></div>
			<?php echo $this->form->getInput('description'); ?>
		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start','reservation-sliders-'.$this->item->id, array('useCookie' => 1, 'allowAllClose'=>true)); ?>
		<?php echo $this->loadTemplate('period'); ?>
		<?php if (empty($this->item->id)) echo $this->loadTemplate('recur'); ?>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
