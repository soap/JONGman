<?php 
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
$js = "
jQuery('.collapse').on('show.bs.collapse', function(){
  var i = jQuery(this).parent().find('i')
  alert(i);
  i.toggleClass('fa-caret-right fa-caret-down');
}).on('hide.bs.collapse', function(){
  var i = jQuery(this).parent().find('i')
  i.toggleClass('fa-caret-down fa-caret-right');
});";

JFactory::getDocument()->addScriptDeclaration($js);
?>
<div class="span12">
<?php
$fieldSets = $this->form->getFieldsets('reservation_custom_fields');

foreach ($fieldSets as $fieldSet) : 
?>
	<fieldset>
    	<legend><?php echo JText::_($fieldSet->label) ?></legend>
	<?php foreach ($this->form->getFieldset($fieldSet->name) as $field) : ?>
		<?php $displayData = array('field'=>$field, 'bootstrap3'=>true); ?>
		<?php echo JLayoutHelper::render('edit.collapsible', $displayData, JPATH_COMPONENT.'/layouts')?>
	<?php endforeach; ?>
	</fieldset>
<?php
endforeach;
?>
</div>