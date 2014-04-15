<?php
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldTimeInterval extends JFormField
{
	protected $type = 'TimeInterval';
	
	public function getInput()
	{
        // Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}
		
		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			
		}
		/* Hidden element to store old value, if required is true */
		if ( ($this->element['required'] == 'true') || ($this->element['required']=='1') ) { 
			$html[] = '<input type="hidden" id="old_timeinterval" name="old_timeinterval" value="'.$this->value.'">';
		}
		return implode("\n", $html);		
	}
} 
