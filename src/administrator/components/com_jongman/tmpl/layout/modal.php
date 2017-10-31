<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

defined('_JEXEC') or die;

// @deprecated 4.0 the function parameter, the inline js and the buttons are not needed since 3.7.0.
$function  = JFactory::getApplication()->input->getCmd('function', 'jEditLayout_' . (int) $this->item->id);

// Function to update input title when changed
JFactory::getDocument()->addScriptDeclaration('
	function jEditLayoutModal() {
		if (window.parent && document.formvalidator.isValid(document.getElementById("item-form"))) {
			return window.parent.' . $this->escape($function) . '(document.getElementById("jform_title").value);
		}
	}
');
?>
<button id="applyBtn" type="button" class="hidden" onclick="Joomla.submitbutton('layout.apply'); jEditLayoutModal();"></button>
<button id="saveBtn" type="button" class="hidden" onclick="Joomla.submitbutton('layout.save'); jEditLayoutModal();"></button>
<button id="closeBtn" type="button" class="hidden" onclick="Joomla.submitbutton('layout.cancel');"></button>

<div class="container-popup">
    <?php $this->setLayout('edit'); ?>
    <?php echo $this->loadTemplate(); ?>
</div>