<?php
/**
 * @version: $Id$
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

require_once(JPATH_COMPONENT.'/helpers/reservation.php');

/**
 * Reservation Subcontroller.
 *
 * @package     JONGman
 * @subpackage  Frontend
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @since       1.0
 */
class JongmanControllerReservation extends JControllerForm
{
	protected $view_list = 'schedule';
	
	protected $view_item = 'reservation';
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('updateinstance', 'save');
		$this->registerTask('updatefull', 'save');
		$this->registerTask('updatefuture', 'save');
	}
	
	public function edit($key = null, $urlVar = null)
	{
		$input = JFactory::getApplication()->input;
		$input->set('layout', 'edit');
		return parent::edit($key, $urlVar);
		
	}
	
	/**
	 * Method to cancel an edit.
	 * @param   string  $key  The name of the primary key of the URL variable.
	 * @return  boolean  True if access level checks pass, false otherwise.
	 */
	public function cancel($key = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$table = $model->getTable();
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";

		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		$recordId = JRequest::getInt($key);

		// Attempt to check-in the current record.
		if ($recordId)
		{
			// Check we are holding the id in the edit list.
			if (!$this->checkEditId($context, $recordId))
			{
				// Somehow the person just went to the form - we don't allow that.
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
				$this->setMessage($this->getError(), 'error');

				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);

				return false;
			}

			if ($checkin)
			{
				if ($model->checkin($recordId) === false)
				{
					// Check-in failed, go back to the record and display a notice.
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
					$this->setMessage($this->getError(), 'error');

					$this->setRedirect(
						JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item
							. $this->getRedirectToItemAppend($recordId, $key), false
						)
					);

					return false;
				}
			}
		}

		// Clean the session data and redirect.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context . '.data', null);

		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);

		return true;
	}
	
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Initialise variables.
		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$table = $model->getTable();
		$data  = JRequest::getVar('jform', array(), 'post', 'array');
		$checkin = property_exists($table, 'checked_out');
		$context = "$this->option.edit.$this->context";
		$task = $this->getTask();

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		$recordId = JRequest::getInt($urlVar);

		if (!$this->checkEditId($context, $recordId))
		{
			// Somehow the person just went to the form and tried to save it. We don't allow that.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
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

		switch ($this->getTask()) {
			case 'updateinstance':
				$updatescope = 'this';
				break;
			case 'updatefull':
				$updatescope = 'full';
				break;
			case 'updatefuture':
				$updatescope = 'future';
				break;
			default:
				$updatescope = 'none';
				break;
		}
		
		// pass for validate usage 
		$data['updatescope'] = $updatescope;
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
				if ($errors[$i] instanceof Exception)
				{
					$app->enqueueMessage($errors[$i]->getMessage(), 'warning');
				}
				else
				{
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

		if ($updatescope === 'none'){
			// Attempt to save the data.
			if (!$model->save($validData))
			{
				// Save the data in the session.
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
		}
		else{
			if (!$model->update($validData, $updatescope))
			{
				// Save the data in the session.
				$app->setUserState($context . '.data', $validData);

				// Redirect back to the edit screen.
				$this->setError(JText::sprintf('COM_JONGMAN_RESERVATION_ERROR_UPDATE_FAILED', $model->getError()));
				$this->setMessage($this->getError(), 'error');

				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
				);
				return false;
			}
		}
		// Save succeeded, so check-in the record.
		if ($checkin && $model->checkin($validData[$key]) === false)
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Check-in failed, so go back to the record and display a notice.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKIN_FAILED', $model->getError()));
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

		// Clear the record id and data from the session.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context . '.data', null);

		// Redirect to the list screen.
		$this->setRedirect($this->getReturnPage());

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);

		return true;
	}	
	
	/**
	 * Check if user is allowed to reserve for a resource
	 * @see JControllerForm::allowAdd()
	 */
	protected function allowAdd($data=array())
	{
		$allowed = parent::allowAdd($data);
		if (!$allowed) return $allowed;
		// Check for more 
		
		return true;
	}
	
	protected function allowEdit($data=array(), $key = 'id')
	{
		$owner_id = 0;
		if (is_array($data) && isset($data['id'])) {
			$model = $this->getModel();
			$reservationInstance = $model->getItem($data['id']);
			$owner_id = $reservationInstance->owner_id;	
		}
		$asset = 'com_jongman';
		$actions = JongmanHelper::getActions($asset);
		if ($actions->get('core.edit')) return true;
		
		if (($owner_id > 0) && $owner_id == JFactory::getUser()->id) {
			if ($actions->get('core.edit.own')) return true;
		}
		
		return false;
	} 
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') 
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$input = JFactory::getApplication()->input;
		
		$resource_id = $input->getInt('rid', null);
		$schedule_id = $input->getInt('sid', null);
		
		$start_date = $input->getString('sd', null);
		$end_date = $input->getString('ed', null);
		
		$return = $this->getReturnPage();
		
		if ($resource_id !== null) {
			$append .= '&resource_id='.$resource_id;
		}
		
		if ($schedule_id !== null) {
			$append .= '&schedule_id='.$schedule_id;
		}
		
		if ($start_date !== null) {
			$append .= '&start='.$start_date;
		}
		
		if ($end_date !== null) {
			$append .= '&end='.$end_date;
		}
		
		if ($return !== null) {
			$append .= '&return='.base64_encode($return);
		}
		
		return $append;
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		
		$app = JFactory::getApplication();
		$schedule_id = $app->input->getInt('schedule_id', null);
		if (!empty($schedule_id)) {
			$append .= '&layout=calendar&id='.$schedule_id;	
		}
		
		return $append;
		
	}
	
	protected function getReturnPage()
	{
		$return = JRequest::getVar('return', null, 'default', 'base64');

        return base64_decode($return);
	}
}