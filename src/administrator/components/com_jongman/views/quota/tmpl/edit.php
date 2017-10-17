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
JHtml::_('jmhtml.script.form');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidator');
JHtml::_('behavior.keepalive');

$js =  "window.addEvent('domready', function() {
    	document.formvalidator.setHandler('abovezerodecimal', function (value) {
           		regex=/^[1-9]?[0-9]{0,3}(\.[0-9]{1,2})?/;
                return regex.test(value);
    	});
	});";
JFactory::getDocument()->addScriptDeclaration($js);
?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('item-form');
		if (task == 'quota.cancel' || document.formvalidator.isValid(form)) {
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
	method="post" name="adminForm" id="item-form" class="form-validate">
	
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JONGMAN_DETAILS', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span8">
						<?php echo $this->form->renderField('schedule_id')?>
						<?php echo $this->form->renderField('resource_id')?>
						<?php echo $this->form->renderField('group_id')?>
						<?php echo $this->form->renderField('quota_limit')?>
						<?php echo $this->form->renderField('unit')?>
						<?php echo $this->form->renderField('duration')?>			
					</div>
					<div class="span2">
					</div>
				</div>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>		
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="<?php echo htmlspecialchars($this->get('Name'), ENT_COMPAT, 'UTF-8');?>" />
	<?php echo $this->form->getInput('elements'); ?>
	<?php echo JHtml::_('form.token'); ?>
</form>