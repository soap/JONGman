<?php
defined('_JEXEC') or die;
echo JHtml::_('tabs.panel',JText::_("COM_JONGMAN_RESERVATION_GENERAL_LABEL"), 'general');
?>
<fieldset class="panel-form">
	<ul class="adminformlist">
	<li>
		<?php echo $this->form->getLabel('id')?>
		<?php echo $this->form->getInput('id')?>
	</li>
	<li>
		<?php echo $this->form->getLabel('resource_id')?>
		<?php echo $this->form->getInput('resource_id')?>
	</li>
	<li>
		<?php echo $this->form->getLabel('reserved_for')?>
		<?php echo $this->form->getInput('reserved_for')?>
	</li>
	<li>
		<?php echo $this->form->getLabel('title')?>
		<?php echo $this->form->getInput('title')?>
	</li>
	<li>
		<?php echo $this->form->getLabel('alias')?>
		<?php echo $this->form->getInput('alias')?>
	</li>
	<div class="clr" />
	<?php echo $this->form->getLabel('description')?>
	<div class="clr" />
	<?php echo $this->form->getInput('description')?>		
	<li><?php echo $this->form->getInput('schedule_id')?></li>
	<li><?php echo $this->form->getInput('checked_out')?></li>
	<li><?php echo $this->form->getInput('checked_out_time')?></li>
	<li><?php echo $this->form->getInput('created_user_id')?></li>
	</ul>
</fieldset>
