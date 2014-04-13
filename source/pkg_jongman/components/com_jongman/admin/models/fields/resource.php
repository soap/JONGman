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

class JFormFieldResource extends JFormFieldList {

    public $type = 'Resource';
    
    protected $schedule;
    protected $quota;

    public function getInput() {
        // Initialize variables.
		$html = array();
		$attr = '';
		
		$hidden = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="" />';

        $attr .= $this->element['class']                         ? ' class="'.(string) $this->element['class'].'"'          : '';
        $attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"'                                   : '';
        $attr .= $this->element['size']                          ? ' size="'.(int) $this->element['size'].'"'               : '';
        $attr .= $this->multiple                                 ? ' multiple="multiple"'                                   : '';
        $attr .= $this->element['onchange']                      ? ' onchange="' .(string) $this->element['onchange'] . '"' : '';
		
		$schedule = (int) $this->form->getValue('schedule_id');
		$quota = (string) $this->element['quota'];
		
		$this->schedule = $schedule;
		// Set the required and validation options.
		$this->quota = ($quota == 'true' || $quota == '1');
       	
		if (!$schedule && !$quota) {
            // Cant get list without at least a schedule id.
            $this->form->setValue($this->element['name'], null, '');
            return '<span class="readonly">' . JText::_('COM_JONGMAN_FIELD_SCHEDULE_REQ') . '</span>' . $hidden;
        }
		// Get the field options.
		$options = (array) $this->getOptions();
       	
		// Return if no options are available.
        if (count($options) == 0) {
            $this->form->setValue($this->element['name'], null, '');
            return '<span class="readonly">' . JText::_('COM_JONGMAN_FIELD_RESOURCE_EMPTY') . '</span>' . $hidden;
        }

		return JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
    }
    
    protected function getOptions() {
    	//get predefined option in xml file (FORM Definition) 
    	if (empty($this->schedule)) {
			$staticOptions = (array) parent::getOptions();
    	}else{
    		$staticOptions = array();
    	}
		
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('id as value, title as text')
        		->from('#__jongman_resources ');
        if (!empty($this->schedule))
        	$query->where('schedule_id = '.$this->schedule);
        $query->order('title asc');
        		
        if (!JFactory::getApplication()->isAdmin()) {
        	$query->where('published = 1');
        }
        $dbo->setQuery($query);
        
        $options = $dbo->loadObjectList();
        return array_merge(
        	$staticOptions, $options);
    }    
    
}
