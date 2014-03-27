<?php
defined('_JEXEC') or die;

class JFormFieldDatetimepicker extends JFormField
{
	public $type = 'Datetimepicker';

	public function getInput()
	{
		// Initialize some field attributes.
		// Initialize some field attributes.
		$format = $this->element['format'] ? (string) $this->element['format'] : '%Y-%m-%d H:i';
		
		// Build the attributes array.
		$attributes = array();
		if ($this->element['size'])
		{
			$attributes['size'] = (int) $this->element['size'];
		}
		if ($this->element['maxlength'])
		{
			$attributes['maxlength'] = (int) $this->element['maxlength'];
		}
		if ($this->element['class'])
		{
			$attributes['class'] = (string) $this->element['class'];
		}
		if ((string) $this->element['readonly'] == 'true')
		{
			$attributes['readonly'] = 'readonly';
		}
		if ((string) $this->element['disabled'] == 'true')
		{
			$attributes['disabled'] = 'disabled';
		}
		if ($this->element['onchange'])
		{
			$attributes['onchange'] = (string) $this->element['onchange'];
		}
		
		// Get some system objects.
		$config = JFactory::getConfig();
		$user = JFactory::getUser();
		
		// Convert a date to UTC based on the user timezone.
		if (intval($this->value))
		{
			// Get a date object based on the correct timezone.
			$date = JFactory::getDate($this->value, 'UTC');
			$date->setTimezone(new DateTimeZone($user->getParam('timezone', $config->get('offset'))));
		
			// Transform the date string.
			$this->value = $date->format('Y-m-d H:i:s', true, false);
		}


	}
	
	protected fucntion getJavaScript()
	{
		
	}
	
	
	protected function getTimeslots()
	{
		
	}
}