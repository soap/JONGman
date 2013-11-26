<?php
/**
 * @version     $Id$
 * @package     JONGman
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
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
		var form = document.id('resource-form');
		if (task == 'resource.cancel' || document.formvalidator.isValid(form)) {
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
<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=edit&id='.(int) $this->item->id); ?>" method="post" name="adminForm" id="resource-form" class="form-validate">
    <div class="width-60 fltlft">
        <fieldset class="adminform">
            <legend><?php echo empty($this->item->id) ? JText::_('COM_JONGMAN_RESOURCE_NEW') : JText::sprintf('COM_JONGMAN_RESOURCE_EDIT', $this->item->id); ?></legend>
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('id'); ?>
                    <?php echo $this->form->getInput('id'); ?></li>
                    
                <li><?php echo $this->form->getLabel('schedule_id'); ?>
                    <?php echo $this->form->getInput('schedule_id'); ?></li>
                       
                <li><?php echo $this->form->getLabel('title'); ?>
                    <?php echo $this->form->getInput('title'); ?></li>

                <li><?php echo $this->form->getLabel('alias'); ?>
                    <?php echo $this->form->getInput('alias'); ?></li>

                <li><?php echo $this->form->getLabel('location'); ?>
                    <?php echo $this->form->getInput('location'); ?></li>
                
                <li><?php echo $this->form->getLabel('contact_info'); ?>
                    <?php echo $this->form->getInput('contact_info'); ?></li>
                
                <li><?php echo $this->form->getLabel('note'); ?>
                    <?php echo $this->form->getInput('note'); ?></li>                          

                <li><?php echo $this->form->getLabel('access'); ?>
                    <?php echo $this->form->getInput('access'); ?></li>
				
                <?php if ($this->canDo->get('core.admin')): ?>
				<li><span class="faux-label"><?php echo JText::_('JGLOBAL_ACTION_PERMISSIONS_LABEL'); ?></span>
					<div class="button2-left"><div class="blank">
						<button type="button" onclick="document.location.href='#access-rules';">
							<?php echo JText::_('JGLOBAL_PERMISSIONS_ANCHOR'); ?>
						</button>
					</div></div>
				</li>
				<?php endif; ?>                    
            </ul>
        </fieldset>
    </div>
    <div class="width-40 fltrt">
    	<?php echo JHtml::_('sliders.start','resource-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
    	<?php echo $this->loadTemplate('params'); ?>
    	<?php echo JHtml::_('sliders.panel',JText::_('COM_JONGMAN_RESOURCE_FIELDSET_PUBLISHING'), 'resource-publishing'); ?>
    	<fieldset class="adminform">
    		<ul class="adminformlist">
          
                <li><?php echo $this->form->getLabel('need_approval'); ?>
                    <?php echo $this->form->getInput('need_approval'); ?></li>

                <li><?php echo $this->form->getLabel('allow_multi'); ?>
                    <?php echo $this->form->getInput('allow_multi'); ?></li>
                    
    			<li><?php echo $this->form->getLabel('published'); ?>
                	<?php echo $this->form->getInput('published'); ?></li>
           	</ul>
    	</fieldset>
    	<?php echo JHtml::_('sliders.end'); ?>
    </div>
    
    <div class="clr"></div>
	<?php if ($this->canDo->get('core.admin')): ?>
		<div class="width-100 fltlft">
			<?php echo JHtml::_('sliders.start','resource-permissions-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
				<?php echo JHtml::_('sliders.panel',JText::_('COM_JONGMAN_RESOURCE_FIELDSET_RULES'), 'access-rules'); ?>
				<fieldset class="panelform">
					<?php echo $this->form->getLabel('rules'); ?>
					<?php echo $this->form->getInput('rules'); ?>
				</fieldset>
			<?php echo JHtml::_('sliders.end'); ?>
		</div>
	<?php endif; ?>
    <input type="hidden" name="task" value=""/>
    <?php echo JHtml::_('form.token'); ?>
</form>
