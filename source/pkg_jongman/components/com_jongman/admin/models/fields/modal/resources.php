<?php
/**
 * @version     $Id$
 * @package     JONGman
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldModal_Resources extends JFormField {

    protected $type = 'Modal_Resources';

    public function getInput() {
        // Initialize variables.
		$html = array();
		$attr = '';
		$link = "index.php?option=com_jongman&amp;view=resources&amp;layout=modal&amp;tmpl=component&amp;field="
			.$this->id."&amp;filter_schedule_id=".(int)$this->element['schedule_id'];
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		
		// Initialize JavaScript field attributes.
		$onchange = (string) $this->element['onchange'];
		
		// Load the modal behavior script.
		JHtml::_('behavior.modal', 'a.modal_' . $this->id);

		// Build the script.
		$script = array();
		$script[] = '	function jSelectResource_' . $this->id . '(id, title) {';
		$script[] = '		var old_id = document.getElementById("' . $this->id . '_id").value;';
		$script[] = '		if (old_id != id) {';
		$script[] = '			document.getElementById("' . $this->id . '_id").value = id;';
		$script[] = '			document.getElementById("' . $this->id . '_name").value = title;';
		$script[] = '			' . $onchange;
		$script[] = '		}';
		$script[] = '		SqueezeBox.close();';
		$script[] = '	}';
		$script[] = '  function jLoadResources() {';
		$script[] = '		var url = document.id("modal_'.$this->id.'").get("rel");';
		$script[] = ' 		SqueezeBox.fromElement(url, {handler:\'iframe\', closeWithOverlay:false, size:{x:800,y:500}});';
		$script[] = '   }';
		
		// Add the script to the document head.
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		
    	// Load the current resource if available.
		$table = JTable::getInstance('Resource', 'JongmanTable');
		if ($this->value)
		{
			$table->load($this->value);
		}
		else
		{
			$table->title = JText::_('COM_JONGMAN_FORM_SELECT_RESOURCE');
		}
		
		// Create a dummy text field with the resource name.
		$html[] = '<div class="fltlft">';
		$html[] = '	<input type="text" id="' . $this->id . '_name"' . ' value="' . htmlspecialchars($table->title, ENT_COMPAT, 'UTF-8') . '"'
			. ' disabled="disabled"' . $attr . ' />';
		$html[] = '</div>';
		
		// Create the select button.
		$html[] = '<div class="button2-left">';
		$html[] = '  <div class="blank">';
		if ($this->element['readonly'] != 'true')
		{
			$html[] = '		<a id="modal_'.$this->id.'" title="' . JText::_('COM_JONGMAN_FORM_CHANGE_RESOURCE') . '"' . ' href="#"';
			$html[] = '		 rel="'.$link.'" onclick="jLoadResources();">' . JText::_('COM_JONGMAN_FORM_CHANGE_RESOURCE') . '</a>';
		}
		$html[] = '  </div>';
		$html[] = '</div>';

		// Create the real field, hidden, that stored the resource id
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . (int) $this->value . '" />';
		return implode("\n", $html);

    }
    
}
