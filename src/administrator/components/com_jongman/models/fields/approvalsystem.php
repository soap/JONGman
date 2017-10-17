<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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

		if (JComponentHelper::isInstalled('com_workflow') && !JComponentHelper::isEnabled('com_workflow')) {
            $tmp = JHtml::_(
                'select.option', '2',
                JText::_('COM_JONGMAN_OPTION_APPROVAL_SYSTEM_JWORKFLOW'), 'value', 'text', true);
            $options[] = $tmp;
        }

		reset($options);
		return $options;
	}

}
