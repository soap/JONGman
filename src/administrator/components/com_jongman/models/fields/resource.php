<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
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
    protected $requireSchedule = false;

    public function getInput() 
    {
        // Initialize variables.
		$html = array();
		$attr = '';
		
		$hidden = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="" />';

        $attr .= $this->element['class']                         ? ' class="'.(string) $this->element['class'].'"'          : '';
        $attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"'                                   : '';
        $attr .= $this->element['size']                          ? ' size="'.(int) $this->element['size'].'"'               : '';
        $attr .= $this->multiple                                 ? ' multiple="multiple"'                                   : '';
        $attr .= $this->element['onchange']                      ? ' onchange="' .(string) $this->element['onchange'] . '"' : '';
		
        $requireSchedule = ($this->element['require_schedule']=='true' || $this->element['require_schedule']=='yes');
        $this->requireSchedule = $requireSchedule;
        $schedule = (int) $this->form->getValue('schedule_id');
        
		if (empty($schedule) && $requireSchedule) {
			$app = JFactory::getApplication();
			$view = $app->input->getCmd('view');
			$schedule = $app->getUserStateFromRequest('com_jongman.'.$view.'.filter.schedule_id', 'filter_schedule_id');
		}
		
		$quota = (string) $this->element['quota'];
		
		$this->schedule = $schedule;
		// Set the required and validation options.
		$this->quota = ($quota == 'true' || $quota == '1');
		$layout = JFactory::getApplication()->input->getCmd('layout');
		if ($layout === 'edit') {
			if ((!$schedule && $requireSchedule)  && !$quota) {
            	// Cant get list without at least a schedule id.
            	$this->form->setValue($this->element['name'], null, '');
            	return '<span class="readonly">' . JText::_('COM_JONGMAN_FIELD_SCHEDULE_REQ') . '</span>' . $hidden;
			}
        }else {
        	if (!$schedule && $requireSchedule) {
 				return '<span class="readonly"></span>';
        	}
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

		$staticOptions = (array) parent::getOptions();
		$user = JFactory::getUser();
        $dbo = JFactory::getDbo();
        $query = $dbo->getQuery(true);
        $query->select('id as value, title as text')
        		->from('#__jongman_resources ');
        if (!empty($this->schedule))
        	$query->where('schedule_id = '.$this->schedule);
        $query->order('title asc');
        		
        if (JFactory::getApplication()->isSite()) {
        	$query->where('published = 1');
        }
        
        if (!$user->authorise('core.admin', 'com_jongman')) {
        	$viewLevels = implode(', ', $user->getAuthorisedViewLevels());
        	$query->where('a.access IN ('.$viewLevels.')');
        }
        
        $dbo->setQuery($query);
        
        $options = $dbo->loadObjectList();
        return array_merge(
        	$staticOptions, $options);
    }       
}