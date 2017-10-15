<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');
require_once JPATH_COMPONENT_SITE.'/libraries/dateutil.class.php';

class JFormFieldJongmanSelectTime extends JFormField {

    protected $type = 'JongmanSelectTime';

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
    
    protected function getOptions() {

        $options = array();
        $min = (int)$this->element['min'];
        $max = (int)$this->element['max'];
        $step = (int)$this->element['step'];
        if (empty($min)) $min = 0;
        if (!empty($max) && !empty($step)) {
        	for ($i=$min; $i <= $max; $i+=$step)
			{
            	$options[] = JHtml::_('select.option', (string)$i, DateUtil::formatTime($i, false));
			}

        	return $options;	
        }
        
        $schedule_id = (int)$this->element['schedule_id'];
        if (empty($schedule_id)) 
        {
        	$schedule_id = JRequest::getInt('schedule_id');
        }
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('day_start, day_end, time_span');
        $query->from('#__jongman_schedules');
        $query->where('id = '.$schedule_id);

        $dbo->setQuery($query);
        $result = $dbo->loadObject();
        
        $min = (int)$result->day_start;
        $max = (int)$result->day_end;
        $step = (int)$result->time_span;
		for ($i=$min; $i <= $max; $i+=$step)
		{
            $options[] = JHtml::_('select.option', (string)$i, DateUtil::formatTime($i, false));
		}

        return $options;
    }
}
