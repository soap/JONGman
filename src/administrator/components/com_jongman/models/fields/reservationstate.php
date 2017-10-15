<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldReservationState extends JFormFieldList
{
	protected $type = 'ReservationState';
	

	protected function getOptions()
	{
		$params = JComponentHelper::getParams('com_jongman');
		$workflowEnabled = $params->get('approvalSystem')==2;
		if ($workflowEnabled) {
			return $this->getWorkflowStateOptions();
		}
		return $this->getStateOptions();		
	}
	
	protected function getWorkflowStateOptions()
	{
		$options = array();
		$user    = JFactory::getUser();
		$db      = JFactory::getDbo();
		$query   = $db->getQuery(true);
	
	
		// Get field attributes for the database query
		$state = ($this->element['state']) ? (int) $this->element['state'] : NULL;
	
		// Build the query
		$query->select('id AS value, title as text')
			->from('#__wf_states AS a')
			->where('a.workflow_id=(SELECT workflow_id FROM #__wf_bindings AS b WHERE context='.$db->quote('com_jongman.reservation').')');
	
		// Implement View Level Access.
		if (!$user->authorise('core.admin')) {
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
	
		// Filter by state
		if (!is_null($state)) $query->where('a.published in (' . $db->quote($state).')');

		
		$db->setQuery((string) $query);
		$items = (array) $db->loadObjectList();
	
		// Generate the options
		if (count($items) > 0) {
			$options[] = JHtml::_('select.option', '',
					JText::alt('JALL',
							preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)),
					'value',
					'text'
			);
		}
	
		foreach($items AS $item)
		{
			// Create a new option object based on the <option /> element.
			$opt = JHtml::_('select.option', (string) $item->value,
					JText::alt(trim((string) $item->text),
							preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)),
					'value',
					'text'
			);
	
			// Add the option object to the result set.
			$options[] = $opt;
		}
	
		reset($options);
	
		return $options;
	}
	
	protected function getStateOptions()
	{
		require_once(JPATH_ROOT.'/administrator/components/com_jongman/helpers/jongman.php');
		return JongmanHelper::getReservationOptions();
	}
}