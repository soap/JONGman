<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Methods supporting a list of schedules records.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jongman
 * @since		1.6
 */
class JongmanModelSchedule extends JModelAdmin
{
    
	/**
	 *
	 * Check if user can save a record
	 * @param unknown $data
	 * @param string $key
	 * @return boolean
	 */
	protected function canSave($data = array(), $key = 'id')
	{
		return JFactory::getUser()->authorise('core.edit', $this->option);
	}
	
    function getTable($type = 'Schedule', $prefix = 'JongmanTable', $config = array() )
    {
        return JTable::getInstance($type, $prefix, $config);        
    }

    /**
     * Prepare data before save
     */
    protected function prepareTable($table) 
    {
    	$table->name		= htmlspecialchars_decode($table->name, ENT_QUOTES);
		$table->alias		= JApplication::stringURLSafe($table->alias);

		if (empty($table->alias)) {
			$table->alias = JApplication::stringURLSafe($table->name);
		}
    }
    
    function preprocessForm(JForm $form, $data, $group = 'content') {
    	parent::preprocessForm($form, $data, $group);			
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
		$form = $this->loadForm('com_jongman.schedule', 
			'schedule', 
			array('control' => 'jform', 'load_data' => $loadData));
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
		$data = JFactory::getApplication()->getUserState('com_jongman.edit.schedule.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	public function getLayouts()
	{
		
	}
	
	function setDefault(&$pks, $value = 1)
	{
		// Initialise variables.
		$table		= $this->getTable();
		$pks		= (array) $pks;
		$user		= JFactory::getUser();
	
		// Remember that we can set a home page for different languages,
		// so we need to loop through the primary key array.
		foreach ($pks as $i => $pk)
		{
			if ($table->load($pk)) {
				if ($table->default == $value) {
					unset($pks[$i]);
					JError::raiseNotice(403, JText::_('COM_JONGMAN_ERROR_SCHEDULE_ALREADY_DEFAULT'));
				}
				else{
					$table->default = $value;
					if (!$this->canSave($table)) {
						// Prune items that you can't change.
						unset($pks[$i]);
						JError::raiseWarning(403, JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
					}
					elseif (!$table->check()) {
						// Prune the items that failed pre-save checks.
						unset($pks[$i]);
						JError::raiseWarning(403, $table->getError());
					}
					elseif (!$table->store()) {
						// Prune the items that could not be stored.
						unset($pks[$i]);
						JError::raiseWarning(403, $table->getError());
					}
				}
	
			}
			else{
				unset($pks[$i]);
				JError::raiseWarning(403, $table->getError());
			}
		}
	
		// Clean the cache
		$this->cleanCache();
	
		return true;
	}
}