<?php
defined('_JEXEC') or die;

/**
 * Layout variables
 * ---------------------
 * 	$options         : (array)  Optional parameters
 * 	$label           : (string) The html code for the label (not required if $options['hiddenLabel'] is true)
 * 	$input           : (string) The input field html code
 */

?>
<?php

//if (!empty($displayData['options']['showonEnabled']))
//{
//	JHtml::_('jquery.framework');
//	JHtml::_('script', 'jui/cms.js', false, true);
//}
?>

<div class="control-group <?php echo $displayData->class; ?>" <?php //echo $displayData['options']['rel']; ?>>
	<?php //if (empty($displayData['options']['hiddenLabel'])) : ?>
		<div class="control-label"><?php echo $displayData->label; ?></div>
	<?php //endif; ?>
	<div class="controls">
		<div id="<?php echo $displayData->id?>_reload">
			<?php echo $displayData->input; ?>
		</div>
	</div>
</div>