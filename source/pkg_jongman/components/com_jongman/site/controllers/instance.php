<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

class JongmanControllerInstance extends JControllerForm
{
	protected $view_list = 'schedule';
	
	protected $view_item = 'reservation';
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('updateinstance', 'save');
		$this->registerTask('updatefull', 'save');
		$this->registerTask('updatefuture', 'save');
		JFactory::getApplication()->input->set('layout', null);
	}
	
	public function allowEdit($data = array(), $key = 'id') 
	{
		var_dump($_REQUEST);
		$user = JFactory::getUser();
		if (isset($data) && ($data[$key] > 0)) {
			$model = $this->getModel();
			$table = $model->getTable();
			$table->load($data[$key]);
			$series = $model->buildSeries($table->reference_number);
			$resourceId = $series->resourceId();						
			$actions = JongmanHelper::getActions('com_jongman.resource.'.$resourceId);

			if ($actions->get('core.edit')) {
				return true;
			}elseif ($actions->get('core.edit.own')) {
				if ($series->bookedBy()->id == $user->id) {
					return true;
				}elseif ($series->userId() == $user->id) {
					return true;
				}	
			}
			return false;	
		}

		return $user->authorise('core.edit', 'com_jongman') || $user->authorise('core.edit.own', 'com_jongman');
	}
	/**
	 * save only existing reservation instance
	 * @see JControllerForm::save()
	 */
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
				$updatescope = 'this';
				break;
		}

		// pass for validate usage 
		$data['updateScope'] = $updatescope;
		
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
			$tz = JongmanHelper::getUserTimezone();
			$data['start_time'] = JDate::getInstance($data['start_time'], $tz)->format('H:i:s', false);
			$data['end_time'] = JDate::getInstance($data['end_time'], $tz)->format('H:i:s', false); 
			
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

		if (!$model->update($validData))
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
		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_list
				. $this->getRedirectToListAppend(), false
			)
		);

		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);

		return true;
	}		
	
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		
		$app = JFactory::getApplication();
		$schedule_id = $app->input->getInt('schedule_id', null);
		if (!empty($schedule_id)) {
			$append .= '&layout=calendar&id='.$schedule_id;	
		}else{
			
		}
		
		return $append;	
	}	
	
}