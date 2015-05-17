<div class="span12">
<?php
$fieldSets = $this->form->getFieldsets('reservation_custom_fields');

foreach ($fieldSets as $fieldSet) :
?>
<fieldset>
    <legend><?php echo JText::_($fieldSet->label) ?></legend>
	<?php foreach ($this->form->getFieldset($fieldSet->name) as $field) : ?>
    <div class="control-group">
		<?php echo trim($field->title); ?>
        <div class="controls">
			<?php echo $field->input; ?>
        </div>
    </div>
	<?php endforeach; ?>
</fieldset>
<?php
endforeach;
?>
</div>
