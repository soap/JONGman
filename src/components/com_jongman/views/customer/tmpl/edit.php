<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

// Include the component HTML helpers.
JHtml::addIncludePath(JPATH_COMPONENT . '/helpers/html');

JHtml::_('behavior.formvalidator');
JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();
$assoc = JLanguageAssociations::isEnabled();

JFactory::getDocument()->addScriptDeclaration('
	Joomla.submitbutton = function(task)
	{
		if (task == "customer.cancel" || document.formvalidator.isValid(document.getElementById("customer-form")))
		{
			Joomla.submitform(task, document.getElementById("customer-form"));
		}
	};
');
?>

<form action="<?php echo JRoute::_('index.php?option=com_jongman&view=customer&layout=edit&id=' . (int) $this->item->id); ?>" method="post" name="adminForm" id="customer-form" class="form-validate">
	<div class="row">			
		<div class="formelm-buttons btn-toolbar pull-right">
			<?php echo $this->toolbar?>
		</div>
		<div class="pull-left"><?php echo $this->title?></div>
	</div>

	<?php //echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>

	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', empty($this->item->id) ? JText::_('COM_JONGMAN_NEW_CUSTOMER', true) : JText::_('COM_JONGMAN_EDIT_CUSTOMER', true)); ?>
		<div class="row">
			<div class="span7">
                <?php echo $this->form->renderField('name'); ?>
				<?php echo $this->form->renderField('image'); ?>
				<?php echo $this->form->renderField('address'); ?>
				<?php echo $this->form->renderField('suburb'); ?>
				<?php echo $this->form->renderField('state'); ?>
				<?php echo $this->form->renderField('postcode'); ?>
				<?php echo $this->form->renderField('country'); ?>
				<?php echo $this->form->renderField('telephone'); ?>
				<?php echo $this->form->renderField('mobile'); ?>
				<?php echo $this->form->renderField('fax'); ?>
				<?php echo $this->form->renderField('webpage'); ?>
				<?php echo $this->form->renderField('email_to'); ?>
			</div>
			<div class="span5">
				<?php //echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'publishing', JText::_('JGLOBAL_FIELDSET_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('joomla.edit.publishingdata', $this); ?>
			</div>
			<div class="span6">
				<?php //echo JLayoutHelper::render('joomla.edit.metadata', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>

		<?php //echo JLayoutHelper::render('joomla.edit.params', $this); ?>

		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="tmpl" value="component" />
	<?php echo JHtml::_('form.token'); ?>
</form>

