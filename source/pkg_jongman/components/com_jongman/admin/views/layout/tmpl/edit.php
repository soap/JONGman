<?php
defined('_JEXEC') or die;

JHtml::_('behavior.tooltip');
JHtml::_('behavior.modal');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
JHtml::_('stylesheet', 'com_jongman/jongman/schedule.css', false, true, false, false, false);
?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('layout-form');
		if (task == 'layout.cancel' || document.formvalidator.isValid(form)) {
			Joomla.submitform(task, form);
		}
		else {
			<?php JText::script('COM_COM_JONGMAN_ERROR_N_INVALID_FIELDS'); ?>
			// Count the fields that are invalid.
			var elements = form.getElements('fieldset').concat(Array.from(form.elements));
			var invalid = 0;

			for (var i = 0; i < elements.length; i++) {
				if (document.formvalidator.validate(elements[i]) == false) {
					valid = false;
					invalid++;
				}
			}

			alert(Joomla.JText._('COM_COM_JONGMAN_ERROR_N_INVALID_FIELDS').replace('%d', invalid));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_jongman&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="layout-form" class="form-validate">
	<div class="row-fluid">
		<div class="width-60 fltlft span7">
			<fieldset class="adminform">
				<ul class="adminformlist unstyled">
					<li><?php echo $this->form->getLabel('id')?> <?php echo $this->form->getInput('id')?>
					</li>
					
					<li><?php echo $this->form->getLabel('title'); ?> <?php echo $this->form->getInput('title'); ?>
					</li>

					<li><?php echo $this->form->getLabel('alias'); ?> <?php echo $this->form->getInput('alias'); ?>
					</li>

					<li><?php echo $this->form->getLabel('timezone'); ?> <?php echo $this->form->getInput('timezone'); ?>
					</li>

					<li><?php echo $this->form->getLabel('published'); ?> <?php echo $this->form->getInput('published'); ?>
					</li>

					<li><?php echo $this->form->getLabel('access'); ?> <?php echo $this->form->getInput('access'); ?>
					</li>

					<li><?php echo $this->form->getLabel('language'); ?> <?php echo $this->form->getInput('language'); ?>
					</li>

					<li><?php echo $this->form->getLabel('note'); ?> <?php echo $this->form->getInput('note'); ?>
					</li>
				</ul>
			</fieldset>
		</div>
	<div class="width-40 fltrt span4">
		<?php echo JHtml::_('sliders.start','layout-sliders-'.$this->item->id, array('useCookie' => 1)); ?>

		<?php echo $this->loadTemplate('params'); ?>

		<?php echo $this->loadTemplate('metadata'); ?>
		<?php echo JHtml::_('sliders.end'); ?>
		<div class="clr"></div>
	</div>
	<div class="clr"></div>
	</div>
	<div class="width-100 fltlft span12">
		<?php echo JHtml::_('sliders.start','layout-timeslot-sliders-'.$this->item->id, array('useCookie'=>1)); ?>
		<?php echo JHtml::_('sliders.panel',JText::_('COM_JONGMAN_TIMESLOT_FIELDSET_TIMESLOTS'), 'layout-timeslots'); ?>
		<fieldset class="panelform">
			<?php echo $this->form->getLabel('timeslots'); ?>
			<div id="jform_timeslots_element">
            	<div id="jform_timeslots_reload" style="clear: both;"> 
					<?php echo $this->form->getInput('timeslots'); ?>
				</div>
			</div>
		</fieldset>
		<?php echo JHtml::_('sliders.end'); ?>
	</div>

	<?php echo $this->form->getInput('elements'); ?>
	<input type="hidden" name="view" value="layout" />
	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<script>
		function reloadTimeSlot() {
			SqueezeBox.close();	
			jQuery(function() {
			    jQuery.ajaxSetup({
			        error: function(jqXHR, exception) {
			            if (jqXHR.status === 0) {
			                alert('Not connect.\n Verify Network.');
			            } else if (jqXHR.status == 404) {
			                alert('Requested page not found. [404]');
			            } else if (jqXHR.status == 500) {
			                alert('Internal Server Error [500].');
			            } else if (exception === 'parsererror') {
			                alert('Requested JSON parse failed.');
			            } else if (exception === 'timeout') {
			                alert('Time out error.');
			            } else if (exception === 'abort') {
			                alert('Ajax request aborted.');
			            } else {
			                alert('Uncaught Error.\n' + jqXHR.responseText);
			            }
			        }
			    });
			});

			jQuery.ajax({
				url: 'index.php?option=com_jongman&task=timeslot.layout&format=json&tmpl=component&layout_id=<?php echo $this->item->id?>',
				dataType:'json',
				data:{},
				success: function (data)
				{
					var items = [];
					var row = jQuery('#slotLayout');
					row.empty();
					var items = [];
					jQuery.map(data.periods, function (item)
					{
						if (item.availability_code=='2') {
							items.push('<td class="unreservable clickres">' + item.label + '</td>');
						}else{
							items.push('<td class="reservable clickres">' + item.label + '</td>');
						}
					});
					row.html(items.join(''));
				
				},
				async: false
			});
		}
</script>