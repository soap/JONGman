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

JFormHelper::loadFieldClass('list');

class JFormFieldSchedule extends JFormFieldList {

    protected $type = 'Schedule';

    public function getInput() {
        // Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}
		/* Hidden element to store old value, if required is true */
		if ( ($this->element['required'] == 'true') || ($this->element['required']=='1') ) { 
			$html[] = '<input type="hidden" id="old_schedule_id" name="old_schedule_id" value="'.$this->value.'">';
		}
		return implode("\n", $html);
    }
    
    protected function getOptions() {
    	//get predefined option in xml file (FORM Definition) 
		$staticOptions = parent::getOptions();
		
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('id as value, name as text')
        		->from('#__jongman_schedules ')
        		->order('name asc');
        $dbo->setQuery($query);
        
        $options = $dbo->loadObjectList();
        return array_merge(
        	$staticOptions, $options);
    }
}
