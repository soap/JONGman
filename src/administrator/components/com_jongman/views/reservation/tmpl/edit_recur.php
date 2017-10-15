<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

echo JHtml::_('sliders.panel',JText::_("COM_JONGMAN_RESERVATION_REPEATATION_LABEL"), 'recur');
?>
<fieldset class="panelform">
	<ul class="adminformlist">
		<li>
			<?php echo $this->form->getLabel('repeat_interval')?>
			<?php echo $this->form->getInput('repeat_interval')?>
		</li>
		<li>
			<div id="repeat_frequency_div">
			<?php echo $this->form->getLabel('repeat_frequency')?>
			<?php echo $this->form->getInput('repeat_frequency')?>
			<?php echo $this->form->getInput('frequency_unit')?>
			</div>
		</li>
		<li>
			<div id="repeat_days_div">
			<?php echo $this->form->getLabel('repeat_day')?>
			<?php echo $this->form->getInput('repeat_day')?>
			</div>
		</li>
		<li>
			<div id="repeat_until_div">
			<?php echo $this->form->getLabel('repeat_until_date')?>
			<?php echo $this->form->getInput('repeat_until_date')?>
			</div>
		</li>
	</ul>
</fieldset>