<?php
defined('_JEXEC') or die;
echo JHtml::_('sliders.panel', JText::_('COM_JONGMAN_METADATA_FIELDSET_LABEL'), 'metadata-options'); ?>
<fieldset class="panelform">
	<ul class="adminformlist">
		<li>
			<?php echo $this->form->getLabel('metadesc'); ?>
			<?php echo $this->form->getInput('metadesc'); ?>
		</li>
	
		<li>
			<?php echo $this->form->getLabel('metakey'); ?>
			<?php echo $this->form->getInput('metakey'); ?>
		</li>
	
		<?php if ($this->item->created) : ?>
			<li>
				<?php echo $this->form->getLabel('created'); ?>
				<?php echo $this->form->getInput('created'); ?>
			</li>
		<?php endif; ?>

		<?php if (intval($this->item->modified)) : ?>
			<li>
				<?php echo $this->form->getLabel('modified'); ?>
				<?php echo $this->form->getInput('modified'); ?>
			</li>
		<?php endif; ?>
	</ul>
</fieldset>