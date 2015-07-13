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
		$this->registerTask('deleteinstance', 'delete');
		$this->registerTask('deletefull', 'delete');
		$this->registerTask('deletefuture', 'delete');
		JFactory::getApplication()->input->set('layout', null);
	}
	
	public function edit($key=null, $urlVar=null)
	{
		$this->setReturnPage();
		if (!parent::edit($key, $urlVar)) {
				
			$returnPage = $this->getReturnPage(true);
			if (!empty($returnPage)) {
				$this->setRedirect(JRoute::_($returnPage, false));
			}
		}
	}
	
	public function allowEdit($data = array(), $key = 'id') 
	{
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
				if ($series->userId() == $user->id) {
					return true;
				}elseif ($series->bookedBy()->id == $user->id) {
					return true;
				}	
			}
			return false;	
		}

		return $user->authorise('core.edit', 'com_jongman') || $user->authorise('core.edit.own', 'com_jongman');
	}
	
	public function allowDelete($data = array(), $key = 'id') 
	{
		return true;
	}
	
	public function allowView($data = array(), $key = 'id')
	{
		return true;
	}
	
	public function view($key='reference_number', $urlVar='ref')
	{
		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$table = $model->getTable();
		$cid   = $this->input->post->get('cid', array(), 'array');
		$context = "$this->option.view.$this->context";
		
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
		
		// Get the previous record id (if any) and the current record id.
		if ((int)count($cid)) {
			$urlVar = 'id';
			$recordId = $cid[0];
		}else{
			if ($urlVar == 'ref') {
				$referenceNumber = $this->input->getCmd($urlVar);
				$table->load(array('reference_number'=>$referenceNumber));
				$recordId = $table->id;
				$urlVar = 'id';
			}else{
				$recordId =  $this->input->getInt($urlVar);
			}
		}
		
		$this->setReturnPage();
		// Access check.
		if (!$this->allowView(array($key => $recordId), $key))
		{
			$this->setError(JText::_('COM_JONGMAN_ERROR_VIEW_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$return = $this->getReturnPage(true);
			if (!empty($return)) {
				$this->setRedirect(JRoute::_($return, false));
			}else{
				$this->setRedirect(
						JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_list
							. $this->getRedirectToListAppend(), false
						)
				);
			}
			return false;
		}
		

		$app->setUserState($context . '.data', null);
		
		$this->input->set('layout', 'readonly');
		$this->setRedirect(
				JRoute::_(
							'index.php?option=' . $this->option . '&view=reservationitem'
							. $this->getRedirectToItemAppend($recordId, $urlVar), false
					)
			);
		
			return true;		
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

			$return = $this->getReturnPage(true);
			if (!empty($return)) {
				$this->setRedirect(JRoute::_($return, false));
			}else{
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
			}
			return false;
		}

		// Populate the row id from the session.
		$data[$key] = $recordId;

		// Access check.
		if (!$this->allowSave($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
			
			$return = $this->getReturnPage(true);
			if (!empty($return)) {
				$this->setRedirect(JRoute::_($return, false));
			}else{
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
			}
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
		$validData['updatescope'] = $updatescope;
		if (!$model->update2($validData))
		{
			$tz = JongmanHelper::getUserTimezone();
			$validData['start_time'] = JDate::getInstance($validData['start_time'], $tz)->format('H:i:s', false);
			$validData['end_time'] = JDate::getInstance($validData['end_time'], $tz)->format('H:i:s', false);
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);

			// Redirect back to the edit screen.

			$this->setError( JText::sprintf('COM_JONGMAN_RESERVATION_ERROR_UPDATE_FAILED', $model->getError()) );
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
		
		$return = $this->getReturnPage(true);
		if (!empty($return)) {
			$this->setRedirect(JRoute::_($return, false));
		}else{
			// Redirect to the list screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);
		}
		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook($model, $validData);

		return true;
	}		
	

	/**
	 * Method to cancel an edit.
	 * @param   string  $key  The name of the primary key of the URL variable.
	 * @return  boolean  True if access level checks pass, false otherwise.
	 */
	public function cancel($key = null)
	{
		$app = JFactory::getApplication();
		if ($app->input->getCmd('view') != 'reservationitem') {
			JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
		}
		// Initialise variables.
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
				
				$return = $this->getReturnPage(true);
				if (!empty($return)) {
					$this->setRedirect(JRoute::_($return, false));
				}else{
					$this->setRedirect(
							JRoute::_(
								'index.php?option=' . $this->option . '&view=' . $this->view_list
								. $this->getRedirectToListAppend(), false
							)
					);
				}
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
	
		$return = $this->getReturnPage(true);
		if (empty($return)) {
			$this->setRedirect(
					JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_list
							. $this->getRedirectToListAppend(), false
					)
			);
		}else{
			$this->setRedirect(JRoute::_($return, false));
		}
	
		return true;
	}	
	
	public function delete()
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
			
			$return = $this->getReturnPage(true);
			if (!empty($return)) {
				$this->setRedirect(JRoute::_($return, false));	
			}else{
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
			}
			return false;
		}
		
		// Populate the row id from the session.
		$data[$key] = $recordId;
		
		// Access check.
		if (!$this->allowDelete($data, $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_DELETE_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');
		
			$return = $this->getReturnPage(true);
			if (!empty($return)) {
				$this->setRedirect(JRoute::_($return, false));
			}else{
				$this->setRedirect(
					JRoute::_(
						'index.php?option=' . $this->option . '&view=' . $this->view_list
						. $this->getRedirectToListAppend(), false
					)
				);
			}
			return false;
		}
		
		switch ($this->getTask()) {
			case 'deleteinstance':
				$updatescope = 'this';
				break;
			case 'deletefull':
				$updatescope = 'full';
				break;
			case 'deletefuture':
				$updatescope = 'future';
				break;
			default:
				$updatescope = 'this';
				break;
		}
		
		// pass for validate usage
		$data['updatescope'] = $updatescope;
		
		// Test whether the data is valid, no need here
		$validData = $data;

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
				else {
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
		
		// checkin before attempt to delete
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

		if (!$model->delete($validData))
		{
			// Save the data in the session.
			$app->setUserState($context . '.data', $validData);
		
			// Redirect back to the edit screen.
			$this->setError(JText::sprintf('COM_JONGMAN_RESERVATION_ERROR_DELETE_FAILED', $model->getError()));
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
				($lang->hasKey($this->text_prefix .'_DELETE_SUCCESS') ? $this->text_prefix : 'JLIB_APPLICATION') . '_DELETE_SUCCESS'
			)
		);
		
		// Clear the record id and data from the session.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context . '.data', null);
		
		$return = $this->getReturnPage(true);
		if (!empty($return)) {
			$this->setRedirect(JRoute::_($return, false));
		}else{
			// Redirect to the list screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);
		}
		return true;
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		
		$app = JFactory::getApplication();
		$schedule_id = $app->input->getInt('schedule_id', null);
		if (empty($schedule_id)) {
			$schedule_id = $app->input->getInt('id');		
		}
	
		$append .= '&layout=calendar&id='.$schedule_id;	
		return $append;	
	}
	
	protected function setReturnPage()
	{
		$app = JFactory::getApplication();
		$return = $app->input->get('return', null, 'base64');
		if (empty($return)) {
			$referer = getenv("HTTP_REFERER");
			if (empty($referer)) return false;
				
			$return = base64_encode($referer);
		}
	
		$app->setUserState('com_jongman.reservation.return_page', $return);
		return true;
	}
	
	protected function clearReturnPage()
	{
		$app = JFactory::getApplication();
		$app->setUserState('com_jongman.reservation.return_page', null);
	}
	
	protected function getReturnPage($clear = false)
	{
		$app = JFactory::getApplication();
		$return = $app->input->get('return', null, 'base64');
		if (empty($return)) {
			$return = $app->getUserState('com_jongman.reservation.return_page');
		}
	
		if ($clear) $this->clearReturnPage();
	
		return base64_decode($return);
	}
}