<?php
defined('_JEXEC') or die;
?>
<fieldset class="batch">
	<legend><?php echo JText::_('COM_JONGMAN_BATCH_OPTIONS');?></legend>
	<p><?php echo JText::_('COM_JONGMAN_BATCH_TIP'); ?></p>

	<button type="submit" onclick="Joomla.submitbutton('banner.batch');">
		<?php echo JText::_('JGLOBAL_BATCH_PROCESS'); ?>
	</button>
	<button type="button" onclick="document.id('batch-begin_time').value='';document.id('batch-end_time').value='';">
		<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>
	</button>
</fieldset>