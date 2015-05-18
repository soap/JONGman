<?php
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');

JFormHelper::loadFieldClass('list');

class JFormFieldApprovalSystem extends JFormFieldList 
{

	protected $type = 'ApprovalSystem';

	protected function getOptions() 
	{	
		$options = array();
		// Create a new option object based on the <option /> element.
		$tmp = JHtml::_(
				'select.option', '1',
				JText::_('COM_JONGMAN_OPTION_APPROVAL_SYSTEM_JONGMAN'), 'value', 'text', false);
		$options[] = $tmp;
		
		$disabled = !JComponentHelper::isInstalled('com_workflow');
		$tmp = JHtml::_(
				'select.option', '2',
				JText::_('COM_JONGMAN_OPTION_APPROVAL_SYSTEM_JWORKFLOW'), 'value', 'text', $disabled);
		$options[] = $tmp;
		
		reset($options);
		return $options;
	}

}
