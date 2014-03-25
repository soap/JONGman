<?php
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldDuration extends JFormField
{
	protected $type = 'Duration';
	
	protected $days = 0;
	protected $hours = 0;
	
	public function getInput()
	{
		$html = array();
		$html[] = '<div class="durationText">';
		if ($this->element['readonly'] == 'true') {
			$html[] = '<span id="durationDays">'.$this->days.'</span>'.JText::plural('COM_JONGMAN_DAYS', $this->days);
			$html[] = '<span id="durationHours">'.$this->hours.'</span>'.JText::plural('COM_JONGMAN_HOURS', $this->hours);
		}else{
			$html[] = '<input id="durationDays" type="text" />';
			$html[] = '<input id="durationHours" type="text"/>'; 	
		}
		
		$html[] = '</div>';
		return implode("\n", $html);
	}
}