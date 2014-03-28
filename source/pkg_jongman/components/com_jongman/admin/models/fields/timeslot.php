<?php
defined('_JEXEC') or die;

jimport('joomla.form.helper');
jimport('jongman.domain.schedulelayout');
jimport('jongman.date.date');
jimport('jongman.date.time');

class JFormFieldTimeslot extends JFormField {

    protected $type = 'Timeslot';
    
    public function getInput()
    {
    	// Load the modal behavior script
        JHtml::_('behavior.modal', 'a.modal_' . $this->id);
    	$html = array();
    	$layout_id = (int) $this->form->getValue('id');
    	
    	$layout = $this->getLayout($layout_id);
    	$slots = $layout->getSlots();
    	$html[]='<div id="legend-container" class="container">';
    	$html[]='	<div class="legend unreservable">'.JText::_('COM_JONGMAN_UNRESERVABLE').'</div>';
    	$html[]='	<div class="legend reservable">'.JText::_('COM_JONGMAN_RESERVABLE').'</div>';
    	$html[]='</div><div style="height: 10px">&nbsp;</div>';
    	$html[]= '<table class="reservations">';
    	$html[]= '	<tr>';
    	foreach ($slots as $period) {
    		if ($period->isReservable()) {
    			$html[]='<td class="reservable clickres">';
    		}else{
    			$html[]='<td class="unreservable clickres">';	
    		}
    		
    		$html[]=$period->start->format('H:i').'</td>';
    	}
    	$html[]= '	</tr>';
    	$html[]= '</table>';
    	
        $link = 'index.php?option=com_jongman&amp;view=timeslot&amp;layout_id='.$layout_id
              . '&amp;layout=modal&amp;tmpl=component'
              . '&amp;function=jmChangeLayout_' . $this->id;
                  	
    	// Create the project select button.
        if ($this->element['readonly'] != 'true') {
            $html[] = '<div class="button2-left">';
            $html[] = '    <div class="blank">';
            $html[] = '<a class="modal_' . $this->id . '" title="' . JText::_('COM_JONGMAN_CHANGE_TIMESLOT') . '"'
                    . ' href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
            $html[] = JText::_('COM_JONGMAN_CHANGE_TIMESLOT') . '</a>';
            $html[] = '    </div>';
            $html[] = '</div>';
        }
    	return implode("\n", $html);
    }

    
    protected function getLayout($layout_id)
    {
    	$timezone = $this->form->getValue('timezone');
    	$layout = new RFLayoutSchedule($timezone);
    	$slots = $this->getTimeSlots($layout_id);

    	foreach ($slots as $slot) {
    	    if ($slot->availability_code == RFSchedulePeriodTypes::RESERVABLE) {
    			$layout->appendPeriod(RFTime::parse($slot->start_time), RFTime::parse($slot->end_time), $slot->label, $slot->day_of_week);
    		}else{
    			$layout->appendBlockedPeriod(RFTime::parse($slot->start_time), RFTime::parse($slot->end_time), $slot->label, $slot->day_of_week);	
    		}	
    	}
    	
    	return $layout;
    }
    
    protected function getTimeSlots($layout_id) 
    {
		$db 	= JFactory::getDbo();
		$query	= $db->getQuery(true);
		
		$query->select('*')->from('#__jongman_time_blocks')
			->where('layout_id ='.(int)$layout_id)
			->order('id ASC');
			
		$db->setQuery($query);
		$blocks = $db->loadObjectList();
    	return $blocks;
    }
}