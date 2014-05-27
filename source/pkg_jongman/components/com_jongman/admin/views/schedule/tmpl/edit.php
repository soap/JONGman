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
	<div class="row-fluid">
		<div class="width-60 fltlft span7">
			<fieldset class="adminform">
				<legend>
				<?php echo empty($this->item->id) ? JText::_('COM_JONGMAN_SCHEDULE_NEW') : JText::sprintf('COM_JONGMAN_SCHEDULE_EDIT', $this->item->id); ?>
				</legend>
				<ul class="adminformlist unstyled">
					<li><?php echo $this->form->getLabel('id'); ?> <?php echo $this->form->getInput('id'); ?>
					</li>

					<li><?php echo $this->form->getLabel('name'); ?> <?php echo $this->form->getInput('name'); ?>
					</li>

					<li><?php echo $this->form->getLabel('alias'); ?> <?php echo $this->form->getInput('alias'); ?>
					</li>

					<li><?php echo $this->form->getLabel('layout_id'); ?> <?php echo $this->form->getInput('layout_id'); ?>
					</li>

					<li><?php echo $this->form->getLabel('weekday_start'); ?> <?php echo $this->form->getInput('weekday_start'); ?>
					</li>

					<li><?php echo $this->form->getLabel('view_days'); ?> <?php echo $this->form->getInput('view_days'); ?>
					</li>

					<li><?php echo $this->form->getLabel('timezone'); ?> <?php echo $this->form->getInput('timezone'); ?>
					</li>

					<li><?php echo $this->form->getLabel('time_format'); ?> <?php echo $this->form->getInput('time_format'); ?>
					</li>

					<li><?php echo $this->form->getLabel('admin_email'); ?> <?php echo $this->form->getInput('admin_email'); ?>
					</li>

					<li><?php echo $this->form->getLabel('notify_admin'); ?> <?php echo $this->form->getInput('notify_admin'); ?>
					</li>

					<li><?php echo $this->form->getLabel('published'); ?> <?php echo $this->form->getInput('published'); ?>
					</li>

					<li><?php echo $this->form->getLabel('access'); ?> <?php echo $this->form->getInput('access'); ?>
					</li>
				</ul>
			</fieldset>
		</div>
		<div class="width-40 fltrt span5">
		<?php echo JHtml::_('sliders.start','schedule-sliders-'.$this->item->id, array('useCookie' => 1)); ?>

		<?php echo $this->loadTemplate('params'); ?>

		<?php echo $this->loadTemplate('metadata'); ?>
		<?php echo JHtml::_('sliders.end'); ?>
		</div>
	</div>
	<div class="clr" />
	<div class="width-90 fltlft">
    	<?php echo $this->loadTemplate('layout')?>
    </div>
    <input type="hidden" name="task" value=""/>
    <?php echo JHtml::_('form.token'); ?>
</form>