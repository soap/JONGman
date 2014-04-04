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

class JFormFieldTimeoptions extends JFormField {

    protected $type = 'Timeoptions';
    
    private $scheduleId;
    private $resourceId;
	private $periodType;
	
    public function getInput() 
    {	
    	$this->scheduleId = (int) $this->form->getValue('schedule_id');
    	$this->resourceId = (int) $this->form->getValue('resource_id');
    	$this->periodType = $this->element['periodtype'] ? $this->element['periodtype'] : '';
    	if ($this->periodType === '') {
    		
    		return false;
    	} 
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

		// Get a date object based on the correct timezone.
		$user = JFactory::getUser();
		$config = JFactory::getConfig();
		$date = JFactory::getDate($this->value, 'UTC');
		$timezone = $user->getParam('timezone', $config->get('offset'));
		$date->setTimezone(new DateTimeZone($timezone));

		// Transform the date string.
		$this->value = $date->format('H:i:s', true, false);
		
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
        $scheduleId = $this->scheduleId;
        $resourceId = $this->resourceId;

        $user = JFactory::getUser();
        $userTz = $user->getParam('timezone', 'UTC');

        $model = JModelLegacy::getInstance('Schedule', 'JongmanModel');
        $layout = $model->getScheduleLayout($scheduleId, $userTz);
		$periods = $layout->getLayout(new RFDate());
		foreach($periods as $period) {
			if ($this->periodType == 'begin') {
				if ($period->isReservable()) {
        			$options[] = JHtml::_('select.option', $period->begin(), $period->label());
				}
			}else if ($this->periodType == 'end'){
				/*if ($period->beginDate()->isMidnight()) {
					$options[] = JHtml::_('select.option', $period->begin(), $period->label());
				}*/	
				if ($period->isReservable()) {
					$options[] = JHtml::_('select.option', $period->end(), $period->labelEnd());
				}
			}
		}

        return $options;
    }
}
