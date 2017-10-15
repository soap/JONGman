<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * timeslot Subcontroller.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       1.0
*/
class JongmanControllerTimeslot extends JControllerForm
{
	protected $view_item = 'timeslot';
	
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
	
		// Initialise variables.
		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$data  = JRequest::getVar('jform', array(), 'post', 'array');
		$context = "$this->option.edit.$this->context";
		$task = $this->getTask();
		$key = 'layout_id';
		$recordId = JRequest::getInt($key);
		
		$data[$key] = $recordId;
		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
	
			$this->setRedirect(
					JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
			);
	
			return false;
		}
	
		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm($data, false);
	
		if (!$form)
		{
			$app->enqueueMessage($model->getError(), 'error');
	
			return false;
		}

		// Test whether the data is valid.
		$validData = $model->validate($form, $data);
		
		// Check for validation errors.
		if ($validData === false)
		{
			// Get the validation messages.
			$errors = $model->getErrors();
	
			// Push up to three validation messages out to the user.
			for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
			{
				if ($errors[$i] instanceof Exception) {
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else{
					$app->enqueueMessage($errors[$i], 'warning');
				}
			}
	
			// Save the data in the session.
			$app->setUserState($context . '.data', $data);
	
			// Redirect back to the edit screen.
			$this->setRedirect(
				JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);
	
			return false;
		}
	
		// Attempt to save the data.
		if (!$model->save($validData))
		{
		// 	Save the data in the session.
			$app->setUserState($context . '.data', $validData);
	
			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');
	
				$this->setRedirect(
						JRoute::_(
								'index.php?option=' . $this->option . '&view=' . $this->view_item
								. $this->getRedirectToItemAppend($recordId, $urlVar), false
						)
			);
	
			return false;
		}
	
	
		$this->setMessage(
			JText::_(
				($lang->hasKey($this->text_prefix . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS')
				? $this->text_prefix
				: 'JLIB_APPLICATION') . ($recordId == 0 && $app->isSite() ? '_SUBMIT' : '') . '_SAVE_SUCCESS'
			)
		);
		JRequest::setVar('success', '1');
		// Redirect the user and adjust session state based on the chosen task.
		switch ($task) {
			case 'apply':
				// Set the record data in the session.
				$recordId = $model->getState($this->context . '.id');
				$this->holdEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
	
				// Redirect back to the edit screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);
			break;
	
			default:
				// Clear the record id and data from the session.
				$this->releaseEditId($context, $recordId);
				$app->setUserState($context . '.data', null);
	
				// Redirect to the list screen.
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
						. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);
			break;
		}
	
		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);
	
		return true;
	}

	protected function getRedirectToItemAppend($recordId=null, $urlVar='layout_id') 
	{
		$success = JRequest::getInt('success', null);
		if ($success) {
			JRequest::setVar('layout', 'success');	
		}else{
			JRequest::setVar('layout', 'modal');
		}
		
		return parent::getRedirectToItemAppend($recordId, $urlVar);	
	}
	
	public function layout()
	{
		
	}
}