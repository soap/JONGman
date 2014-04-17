<?php
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

class JFormFieldTimeInterval extends JFormField
{
	protected $type = 'TimeInterval';
	
	private $days;
	private $hours;
	private $minutes;
	
	public function getInput()
	{
        // Add the script to the document head.
        $interval = RFTimeInterval::parse($this->value * 60);
        $this->days = $interval->days();
        $this->hours = $interval->hours();
        $this->minutes = $interval->minutes();
        
        $script = $this->getJavascript();
        JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));
		$html = $this->getHtml();	
		return implode("\n", $html);		
	}
	
	protected function getHtml()
	{
		$html = array();
		$attr = '';
		
		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}
		
		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';
		
		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->elemet['readonly'] == 'true') {
			$html[] = '<span class="' . $this->id . '">';
			$html[] = '<input type="text" name="days" value="'.$this->days.'" id="" class="days" size="3" disabled="disabled" maxlength="3" placeholder="days" />';
			$html[] = '<input type="text" name="hours" value="'.$this->hours.'" id="" class="hour" size="2" disabled="disabled" maxlength="2" placeholder="days" />';
			$html[] = '<input type="text" name="minutes" value="'.$this->minutes.'" id="" class="minutes" size="2" disabled="disabled" maxlength="2" placeholder="days" />';
			$html[] = '</span>';
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {

			$html[] = '<span class="' . $this->id . '">';
			$html[] = '<input type="text" name="days" value="'.$this->days.'" id="'.$this->id.'_days" class="days" size="3" maxlength="3" placeholder="day" />';
			$html[] = '<input type="text" name="hours" value="'.$this->hours.'" id="'.$this->id.'_hours" class="hours" size="2" maxlength="2" placeholder="hrs" />';
			$html[] = '<input type="text" name="minutes" value="'.$this->minutes.'" id="'.$this->id.'_minutes" class="minutes" size="2" maxlength="2" placeholder="min" />';
			$html[] = '<input type="hidden" id="'.$this->id.'" name="'.$this->name.'" value="'.$this->value.'"/>';
			$html[] = '</span>';			
		}		
		
		return $html;
	}
	
	protected function getJavaScript()
	{
		$script = array();
		$script[] = "jQuery(document).on(\"change\", \"#{$this->id}_days\", combineInterval_{$this->id});";
		$script[] = "jQuery(document).on(\"change\", \"#{$this->id}_hours\", combineInterval_{$this->id});";
		$script[] = "jQuery(document).on(\"change\", \"#{$this->id}_minutes\", combineInterval_{$this->id});";
		$script[] = "function combineInterval_{$this->id}() { ";
		$script[] = "  var d = jQuery('#{$this->id}_days').val();";
		$script[] =	"  var h = jQuery('#{$this->id}_hours').val();";
		$script[] =	"  var m = jQuery('#{$this->id}_minutes').val();";
		$script[] = "  d = parseInt(d) || 0; ";
		$script[] = "  h = parseInt(h) || 0; ";
		$script[] = "  m = parseInt(m) || 0; ";
		$script[] = "  var i = d * 24 * 60  + h * 60  + m ;";
		$script[] =	"  jQuery('#{$this->id}').val(i);";
		$script[] = "  ";
		$script[] = "};";
		
		return $script;
	}
} 
