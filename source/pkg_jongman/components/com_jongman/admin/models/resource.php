<?php
/**
 * @version		$Id: schedule.php 1 2010-12-03 19:47:09Z prasit gebsaap $
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Methods supporting a list of resources.
 *
 * @package		JONGman
 * @subpackage	com_jongman
 * @since		2.0
 */
class JongmanModelResource extends JModelAdmin
{
    
    function getTable($type = 'Resource', $prefix = 'JongmanTable', $config = array() )
    {
        return JTable::getInstance($type, $prefix, $config);        
    }

    /**
     * Prepare data before save
     */
   	protected function prepareTable($table) 
   	{
    	$table->title		= htmlspecialchars_decode($table->title, ENT_QUOTES);
		$table->alias		= JApplication::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = JApplication::stringURLSafe($table->title);
		}
    }
    /**
	 * Method to get the record form.
	 *
	 * @param	array	$data		Data for the form.
	 * @param	boolean	$loadData	True if the form is to load its own data (default case), false if not.
	 * @return	mixed	A JForm object on success, false on failure
	 * @since	1.6
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm('com_jongman.resource', 'resource', array('control' => 'jform', 'load_data' => $loadData));
		if (empty($form)) {
			return false;
		}

		// Modify the form based on access controls.
		if (!$this->canEditState((object) $data)) {
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('publish_up', 'disabled', 'true');
			$form->setFieldAttribute('publish_down', 'disabled', 'true');
			$form->setFieldAttribute('state', 'disabled', 'true');

			// Disable fields while saving.
			// The controller has already verified this is a record you can edit.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('publish_up', 'filter', 'unset');
			$form->setFieldAttribute('publish_down', 'filter', 'unset');
			$form->setFieldAttribute('state', 'filter', 'unset');
		}

		return $form;
	}
    
	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return	mixed	The data for the form.
	 * @since	1.6
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jongman.edit.resource.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}  

	public function approval($pks, $value=1)
	{
		// Initialise variables.
		$dispatcher = JDispatcher::getInstance();
		$user = JFactory::getUser();
		$table = $this->getTable();
		$pks = (array) $pks;
		
		// Include the content plugins for the change of state event.
		JPluginHelper::importPlugin('content');
		
		// Access checks.
		foreach ($pks as $i => $pk)
		{
			$table->reset();
		
			if ($table->load($pk))
			{
				if (!$this->canEditState($table))
				{
					// Prune items that you can't change.
					unset($pks[$i]);
					JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_EDITSTATE_NOT_PERMITTED'));
					return false;
				}
			}
		}
		
		//$params = new JRegistry($table->params);
		//$params->set('need_approval', $value);
		//$table->params = $params->toString();
		$table->requires_approval = $value;
		if (!$table->store()) {
			$this->setError($table->getError());
			return false;			
		}
		
		$context = $this->option . '.' . $this->name;
		
		// Trigger the onContentChangeState event.
		$result = $dispatcher->trigger($this->event_change_state, array($context, $pks, $value));
		
		if (in_array(false, $result, true))
		{
			$this->setError($table->getError());
			return false;
		}
		
		// Clear the component's cache
		$this->cleanCache();
		
		return true;			
	}
}