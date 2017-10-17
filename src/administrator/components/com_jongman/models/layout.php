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
 * Layout model.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanModelLayout extends JModelAdmin
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
		
	protected function canEditState($record)
	{
		if (($record->default == '1') && ($record->published == '1')) {
			JError::raiseWarning(403, JText::_('COM_JONGMAN_ERROR_CANNOT_DEFAULT_LAYOUT_CHANE_STATE_NOT_ALLOWED'));
			return false;
		}
		$user = JFactory::getUser();
		return $user->authorise('core.edit.state', $this->option);
	}
	/**
	 * Method to get the Layout form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 * @since   2.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			$this->option.'.'.$this->name,
			$this->getName(),
			array('control' => 'jform', 'load_data' => $loadData)
		);

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a Layout.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed    Category data object on success, false on failure.
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($result = parent::getItem($pk)) {

			// Convert the created and modified dates to local user time for display in the form.
			jimport('joomla.utilities.date');
			$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));

			if (intval($result->created)) {
				$date = new JDate($result->created);
				$date->setTimezone($tz);
				$result->created = $date->toSql(true);
			}
			else {
				$result->created = null;
			}

			if (intval($result->modified)) {
				$date = new JDate($result->modified);
				$date->setTimezone($tz);
				$result->modified = $date->toSql(true);
			}
			else {
				$result->modified = null;
			}
		}

		return $result;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name.
	 * @param   array   $config  Configuration array for model.
	 *
	 * @return  JTable  A database object
	 * @since   2.0
	 */
	public function getTable($type = 'Layout', $prefix = 'JongmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   2.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->option.'.edit.'.$this->getName().'.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/** 
	 * Prepare the form before display to the user.
	 *
	 * @param   JForm  $form  The table object for the record.
	 * @param   	   $data  The data binded to $form
	 * @param	string $group group of plugin to trigger
	 * @return  boolean  True if successful, otherwise false and the error is set.
	 * @since   2.0
	 */	 
	 
	protected function preprocessForm(JForm $form, $data, $group='content')
	{
		$user = JFactory::getUser();
		$statesFields = array('published');
		if ( !($user->authorise('core.edit.state', 'com_jongman')) ) {
			foreach($stateFields as $field) {
				$form->setFieldAttribute($field, 'disabled', 'true');
				$form->setFieldAttribute($field, 'filter', 'unset');
			}
		}
		$disableChangeLayout = false;
		if (!empty($data)) {
			if (is_array($data)) {
				if (empty($data['id'])) {
					$disableChangeLayout = true;
				}	
			}else{
				if (empty($data->id)) {
					$disableChangeLayout = true;	
				}
			}
		}
		if ($disableChangeLayout) {
			$form->setFieldAttribute('timeslots', 'readonly', 'true');
			$form->setFieldAttribute('timeslots', 'filter', 'unset');	
		}
 		
		parent::preprocessForm($form, $data, $group);
	}
	
	/** 
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  The table object for the record.
	 *
	 * @return  boolean  True if successful, otherwise false and the error is set.
	 * @since   2.0
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		// Prepare the alias.
		$table->alias = JApplication::stringURLSafe($table->alias);

		// If the alias is empty, prepare from the value of the title.
		if (empty($table->alias)) {
			$table->alias = JApplication::stringURLSafe($table->title);
		}

		// Clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey)) {
			// Only process if not empty.

			// array of characters to remove.
			$strip = array("\n", "\r", '"', '<', '>');
			
			// Remove bad characters.
			$clean = JString::str_ireplace($strip, ' ', $this->metakey); 

			// Create array using commas as delimiter.
			$oldKeys = explode(',', $clean);
			$newKeys = array();
			
			foreach ($oldKeys as $key)
			{
				// Ignore blank keywords
				if (trim($key)) {
					$newKeys[] = trim($key);
				}
			}

 			// Put array back together, comma delimited.
 			$this->metakey = implode(', ', $newKeys);
		}
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
					JError::raiseNotice(403, JText::_('COM_JONGMAN_ERROR_LAYOUT_ALREADY_DEFAULT'));
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