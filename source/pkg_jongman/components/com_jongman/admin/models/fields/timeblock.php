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


class JFormFieldTimeblock extends JFormField {

    protected $type = 'Timeblock';

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
        
        $schedule_id = (int)$this->form->getValue('schedule_id');

				
        return $options;
    }
    
    private function getLayout($shcedule_id) 
    {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select("a.*, l.timezone")
				->from("#__jongman_schedules AS a")
				->join('LEFT', '#__jongman_layouts as l ON l.id=a.layout_id')
				->where("a.id = ".$pk);
		$db->setQuery($query);
			
		$item = $db->loadObject();
		$blocks = $this->getTimeblocks($item->layout_id);
		
		$tz = $schedule->timezone;		
		$layout = new ScheduleLayout($tz);

		foreach($blocks as $period) {
			if ($period->availability_code == 1) {
				$layout->appendPeriod(
					Time::parse($period->start_time), Time::parse($period->end_time), 
					$period->label, $period->day_of_week);
			}else{
				$layout->appendBlockedPeriod(
					Time::parse($period->start_time), Time::parse($period->end_time), 
					$period->label, $period->day_of_week);	
			}	
		}
		

    }   

	private function getTimeblocks($layout_id = null) 
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__jongman_time_blocks')
			->where('layout_id = '.(int)$layout_id)
			->order('id ASC');
			
		$db->setQuery($query);
		
		$rows = $db->loadObjectList();
		
		return $rows;
		
	}
}
