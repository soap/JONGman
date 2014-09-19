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
	
	<?php echo JLayoutHelper::render('joomla.edit.title_alias', $this); ?>
	<div class="form-horizontal">
		<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'details')); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'details', JText::_('COM_JONGMAN_DETAILS', true)); ?>
		<div class="row-fluid">
			<div class="span9">
				<div class="row-fluid form-horizontal-desktop">
					<div class="span8">
						<?php echo $this->form->renderField('timezone')?>			
					</div>
					<div class="span2">
					</div>
				</div>
			</div>
			<div class="span3">
				<?php echo JLayoutHelper::render('joomla.edit.global', $this); ?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTab'); ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'timeslots', JText::_('COM_JONGMAN_TIMESLOT_FIELDSET_TIMESLOTS', true)); ?>
		<div class="row-fluid">
			<div class="span12">
				<?php echo $this->form->renderField('timeslots')?>
			</div>
		</div>
		<?php echo JHtml::_('bootstrap.endTabSet'); ?>
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