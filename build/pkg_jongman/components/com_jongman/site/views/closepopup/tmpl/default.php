<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
?>
<fieldset style="padding-left: 5px ; border: 2px blue;  ">
    <div style="float: right">
        <input type="button" id="close-btn" value="<?php echo JText::_( "COM_JONGMAN_BUTTON_CLOSE" );?>" onClick="onClose()" />
    </div>
    <div id="jm-container">
        <legend>
            <?php echo ($this->success)? JText::_('COM_JONGMAN_RESERVATION_SUCCESS'):JText::_('COM_JONGMAN_RESERVATION_FAILURE')?>
        </legend>
        <?php echo $this->message?>
    </div>
</fieldset>
<script language="javascript">
	<?php if ($this->cancel) : ?>
	window.parent.SqueezeBox.close();
	<?php endif?>
	<?php if ($this->success) :?>
	window.parent.forceReload = true;
	<?php else:?>
	window.parent.forceReload = false;
	<?php endif?>
	function onClose() {
		window.parent.SqueezeBox.close();
	};
</script>