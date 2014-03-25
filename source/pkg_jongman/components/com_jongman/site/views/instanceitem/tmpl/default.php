<?php
defined('_JEXEC') or die;
?>
<div class="reservationbox">
	<div id="reservationDetails">
		<ul class="unstyle">
			<div class="users">
				<label class="label"><?php echo JText::_('COM_JONGMAN_NAME')?></label>
				<?php echo $this->item->owner_name?>
			</div>
			<div class="dates">
				<label class="label"><?php echo JText::_('COM_JONGMAN_DURATION')?></label>
				<?php echo JHtml::date($this->item->start_date, 'Y-m-d H:i:s', true)?> - <?php  echo JHtml::date($this->item->end_date, 'Y-m-d H:i:s', true)?>
			</div>
			<div class="referncenumber">
				<label class="label"><?php echo JText::_('COM_JONGMAN_REFERENCE_NUMBER')?></label>
				<?php echo $this->item->reference_number?>
			</div>
			<div class="title">
				<label class="label"><?php echo JText::_('COM_JONGMAN_TITLE')?></label>
				<?php echo $this->item->title?>
			</div>
			<div class="resources">
				<label class="label"><?php echo JText::_('COM_JONGMAN_RESOURCE')?></label>
			</div>
		</ul>
	</div>
</div>
