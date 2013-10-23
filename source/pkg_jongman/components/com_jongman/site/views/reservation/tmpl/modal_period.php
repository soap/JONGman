<?php
defined('_JEXEC') or die;
echo JHtml::_('tabs.panel',JText::_("COM_JONGMAN_RESERVATION_PERIOD_LABEL"), 'period');
?>
<fieldset class="panel-form">
	<ul class="adminformlist">
		<li><?php echo $this->form->getLabel('start_date')?>
			<?php echo $this->form->getInput('start_date')?>
		</li>
		<li><?php echo $this->form->getLabel('end_date')?>
			<?php echo $this->form->getInput('end_date')?>
		</li>
		<li>
			<?php echo $this->form->getLabel('start_time')?>
			<?php echo $this->form->getInput('start_time')?>
		</li>
		<li>
			<?php echo $this->form->getLabel('end_time')?>
			<?php echo $this->form->getInput('end_time')?>
		</li>
	</ul>
</fieldset>