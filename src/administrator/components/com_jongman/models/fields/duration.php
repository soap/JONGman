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
			$html[] = '<span id="durationDays"  class="input-mini uneditable-input"></span>';
			$html[] = '<span id="durationHours" class="input-mini uneditable-input"></span>';
		}else{
			$html[] = '<input id="durationDays" type="text" />';
			$html[] = '<input id="durationHours" type="text"/>'; 	
		}
		
		$html[] = '</div>';
		return implode("\n", $html);
	}
}