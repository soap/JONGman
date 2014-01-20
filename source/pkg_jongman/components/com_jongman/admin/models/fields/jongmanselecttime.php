<?php
/**
 * @version     $Id$
 * @package     JONGman
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
jimport('jongman.domain.schedulelayout.php');

class JFormFieldJongmanSelectTime extends JFormField {

    protected $type = 'JongmanSelectTime';
    
    private $scheduleId;
    private $resourceId;

    public function getInput() 
    {	
    	$this->scheduleId = (int) $this->form->getValue('schedule_id');
    	$this->resourceId = (int) $this->form->getValue('resource_id');
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

		return implode($html);
    }
    
    protected function getOptions() 
    {
        $options = array();
        $scheduleId = $this->scheduleId;
        $resourceId = $this->resourceId;
        
        $user = JFactory::getUser();
        $userTz = $user->getParam('timezone', 'UTC');
        
        $model = JModelLegacy::getInstance('Schedule', 'JongmanModel');
        $layout = $model->getScheduleLayout($scheduleId, $userTz);
		$periods = $layout->getLayout(new JMDate());        
		foreach($periods as $period) {
			if ($period->isReservable()) {
        		$options[] = JHtml::_('select.option', $period->end(), $period->labelEnd());
			}
		}


        return $options;
    }
}
