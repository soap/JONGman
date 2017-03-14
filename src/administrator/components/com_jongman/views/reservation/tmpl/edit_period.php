<?php
echo JHtml::_('sliders.panel',JText::_(COM_JONGMAN_RESERVATION_PERIOD_LABEL), 'period');
?>
<fieldset class="panelform">
	<ul class="adminformlist">
		<li><?php echo $this->form->getLabel('start_date')?>
			<?php echo $this->form->getInput('start_date')?>
		</li>
		<li><?php echo $this->form->getLabel('end_date')?>
			<?php echo $this->form->getInput('end_date')?>
		</li>
	</ul>
</fieldset>