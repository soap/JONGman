<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('jmhtml.script.form');
?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('blackout-form');
		if (task == 'blackout.cancel' || document.formvalidator.isValid(form)) {
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
	method="post" name="adminForm" id="blackout-form" class="form-validate">
	
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JONGMAN_BLACKOUT_DETAILS', true)); ?>
		<div class="row-fluid">
			<div class="span8">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->renderField('start_date'); ?>
					</div>
					<div class="span6" id="jform_start_time_element">
                		<div id="jform_start_time_reload">
							<?php echo $this->form->getInput('start_time'); ?>
						</div>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span6">
						<?php echo $this->form->renderField('end_date'); ?>
					</div>
					<div class="span6" id="jform_end_time_element">
						<div id="jform_end_time_reload">
							<?php echo $this->form->getInput('end_time'); ?>
						</div>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->renderField('resource_id'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->renderField('title'); ?>
					</div>
				</div>
				<div class="row-fluid form-horizontal-desktop">
					<div class="span12">
						<?php echo $this->form->renderField('conflict_action'); ?>
					</div>
				</div>
			</div>
			<div class="span4">
				<?php if (empty($this->item->id)) : ?>
				<?php echo $this->loadTemplate('repeatoptions')?>
				<?php endif;?>
			</div>

		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php $this->show_options = 1; $this->ignore_fieldsets = array('repeat_options');?>
		<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="<?php echo htmlspecialchars($this->get('Name'), ENT_COMPAT, 'UTF-8');?>" />
	<?php echo $this->form->getInput('elements'); ?>
	<?php echo JHtml::_('form.token'); ?>
</form>
<script type="text/javascript">
<!--
	function onStartDateChange(dateEl) {
		var startDate = dateEl.value;
		var endDate = document.getElementById('jform_end_date').value;
	    if ((Date.parse(startDate) > Date.parse(endDate))) {
	        console.log("End date should be greater than Start date");
	        document.getElementById("jform_end_date").value = startDate;
	    }
	}
//-->
</script>
