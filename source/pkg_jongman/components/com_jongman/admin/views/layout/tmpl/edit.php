<?php
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('stylesheet', 'com_jongman/jongman/schedule.css', false, true, false, false, false);
?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('layout-form');
		if (task == 'layout.cancel' || document.formvalidator.isValid(form)) {
			Joomla.submitform(task, form);
		}
		else {
			<?php JText::script('COM_COM_JONGMAN_ERROR_N_INVALID_FIELDS'); ?>
			// Count the fields that are invalid.
			var elements = form.getElements('fieldset').concat(Array.from(form.elements));
			var invalid = 0;

			for (var i = 0; i < elements.length; i++) {
				if (document.formvalidator.validate(elements[i]) == false) {
					valid = false;
					invalid++;
				}
			}

			alert(Joomla.JText._('COM_COM_JONGMAN_ERROR_N_INVALID_FIELDS').replace('%d', invalid));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="layout-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<ul class="adminformlist">
				<li>
					<?php echo $this->form->getLabel('id')?>
					<?php echo $this->form->getInput('id')?>
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
					<?php echo $this->form->getLabel('timezone'); ?>
					<?php echo $this->form->getInput('timezone'); ?>
				</li>

				<li>
					<?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?>
				</li>

				<li>
					<?php echo $this->form->getLabel('access'); ?>
					<?php echo $this->form->getInput('access'); ?>
				</li>

				<li>
					<?php echo $this->form->getLabel('language'); ?>
					<?php echo $this->form->getInput('language'); ?>
				</li>

				<li>
					<?php echo $this->form->getLabel('note'); ?>
					<?php echo $this->form->getInput('note'); ?>
				</li>
			</ul>

		</fieldset>
	</div>
	<div class="width-40 fltrt">
		<?php echo JHtml::_('sliders.start','layout-sliders-'.$this->item->id, array('useCookie' => 1)); ?>

		<?php echo $this->loadTemplate('params'); ?>

		<?php echo $this->loadTemplate('metadata'); ?>
		<?php echo JHtml::_('sliders.end'); ?>

	</div>
	<div class="clr"></div>

		<div class="width-100 fltlft">
			<?php echo JHtml::_('sliders.start','layout-timeslot-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
				<?php echo JHtml::_('sliders.panel',JText::_('COM_JONGMAN_TIMESLOT_FIELDSET_TIMESLOTS'), 'layout-timeslots'); ?>
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('timeslots'); ?>
					<div id="jform_timeslots_element">
                        <div id="jform_timeslots_reload"> 
							<?php echo $this->form->getInput('timeslots'); ?>
						</div>
					</div>
				</fieldset>
			<?php echo JHtml::_('sliders.end'); ?>
		</div>
	<?php echo $this->form->getInput('elements'); ?>
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<script>
		function reloadTimeSlot() {
			JMform.reload('timeslots', 'layout-form', 'adminForm');	
			SqueezeBox.close();	
		}
</script>