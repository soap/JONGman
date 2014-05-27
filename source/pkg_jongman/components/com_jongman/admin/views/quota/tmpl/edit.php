<?php
// No direct access
defined('_JEXEC') or die;
//JHtml::addIncludePath(JPATH_COMPONENT.'helpers/html');
JHtml::_('jmhtml.script.form');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	window.addEvent('domready', function() {
    	document.formvalidator.setHandler('abovezerodecimal',
            function (value) {
           		regex=/^[1-9]?[0-9]{0,3}(\.[0-9]{1,2})?/;
                return regex.test(value);
    	});
	});
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
	<div class="row-fluid">
		<div class="width-60 fltlft span7">
			<fieldset class="adminform">
				<ul class="adminformlist unstyled">
					<li><?php echo $this->form->getLabel('title'); ?> <?php echo $this->form->getInput('title'); ?>
					</li>
					<li><?php echo $this->form->getLabel('schedule_id'); ?> <?php echo $this->form->getInput('schedule_id'); ?>
					</li>

					<li><?php echo $this->form->getLabel('resource_id'); ?>
						<div id="jform_resource_id_element">
							<div id="jform_resource_id_reload">
							<?php echo $this->form->getInput('resource_id'); ?>
							</div>
						</div>
					</li>

					<li><?php echo $this->form->getLabel('group_id'); ?> <?php echo $this->form->getInput('group_id'); ?>
					</li>

					<li><?php echo $this->form->getLabel('quota_limit'); ?> <?php echo $this->form->getInput('quota_limit'); ?>
					</li>

					<li><?php echo $this->form->getLabel('unit'); ?> <?php echo $this->form->getInput('unit'); ?>
					</li>

					<li><?php echo $this->form->getLabel('duration'); ?> <?php echo $this->form->getInput('duration'); ?>
					</li>

					<li><?php echo $this->form->getLabel('published'); ?> <?php echo $this->form->getInput('published'); ?>
					</li>

					<li><?php echo $this->form->getLabel('access'); ?> <?php echo $this->form->getInput('access'); ?>
					</li>

					<li><?php echo $this->form->getLabel('ordering'); ?> <?php echo $this->form->getInput('ordering'); ?>
					</li>
					<li><?php echo $this->form->getLabel('note'); ?> <?php echo $this->form->getInput('note'); ?>
					</li>
				</ul>

			</fieldset>
		</div>
		<div class="width-40 fltrt span4">
		<?php echo JHtml::_('sliders.start','quota-sliders-'.$this->item->id, array('useCookie' => 1)); ?>

		<?php echo JHtml::_('sliders.end'); ?>

		</div>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="<?php echo htmlspecialchars($this->get('Name'), ENT_COMPAT, 'UTF-8');?>" />
	<?php echo $this->form->getInput('elements'); ?>
	<?php echo JHtml::_('form.token'); ?>
</form>