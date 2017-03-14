<?php
/**
 * @version: $Id$
 * @copyright	Copyright (C) 2009 - 2011 Prasit Gebsaap. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('schedule-form');
		if (task == 'schedule.cancel' || document.formvalidator.isValid(form)) {
			<?php //echo $this->form->getField('body')->save(); ?>
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
<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="schedule-form" class="form-validate">
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
		
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? JText::_('COM_JONGMAN_SCHEDULE_NEW', true) : JText::_('COM_JONGMAN_SCHEDULE_EDIT', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span10">
						<?php echo $this->form->renderField('default'); ?>
						<?php echo $this->form->renderField('layout_id'); ?>
						<?php echo $this->form->renderField('weekday_start'); ?>
						<?php echo $this->form->renderField('view_days'); ?>
						<?php echo $this->form->renderField('timezone'); ?>
						<?php echo $this->form->renderField('time_format'); ?>
						<?php echo $this->form->renderField('admin_email'); ?>
						<?php echo $this->form->renderField('notify_admin'); ?>
					</div>
					<div class="span2">
					</div>
				</div>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
    <input type="hidden" name="task" value=""/>
    <?php echo JHtml::_('form.token'); ?>
</form>