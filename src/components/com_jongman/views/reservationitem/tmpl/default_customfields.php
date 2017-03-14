<div class="span12">
<?php
$fieldSets = $this->form->getFieldsets('reservation_custom_fields');

foreach ($fieldSets as $fieldSet) :
?>
<fieldset>
    <legend><?php echo JText::_($fieldSet->label) ?></legend>
	<?php foreach ($this->form->getFieldset($fieldSet->name) as $field) : ?>
    <div class="control-group">
		<span class="label label-info"><?php echo trim($field->title); ?></span>
        <div class="controls well">
        	<?php 
        	if ($field->type != 'Editor') :
				echo nl2br(htmlspecialchars($field->value)); 
			else:
				echo $field->value;
			endif; 
			?>
        </div>
    </div>
	<?php endforeach; ?>
</fieldset>
<?php
endforeach;
?>
</div>
