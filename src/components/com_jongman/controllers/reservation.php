<?php

defined('_JEXEC') or die;
jimport('jongman.controller.form');

require_once(JPATH_COMPONENT.'/helpers/reservation.php');

/**
 * Reservation SubController.
 *
 * @package     JONGman
 * @subpackage  Frontend
 * @license     GNU General Public License version 2 or later; see LICENSE
 * @since       1.0
 */
class JongmanControllerReservation extends RFControllerForm {

	protected $view_list = 'reservations';
	
	protected $view_item = 'reservation';
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('updateinstance', 'save');
		$this->registerTask('updatefull', 'save');
		$this->registerTask('updatefuture', 'save');
	}
	
	public function add()
	{
		$this->setReturnPage('com_jongman.reservation.return_page');
		$app = JFactory::getApplication();
		$startDate = $app->input->getString('sd', null);
		//if ($startDate == null) $app->input->set('sd', JFactory::getDate()->format('Y-m-d'));
		
		if (!parent::add()) {
			$returnPage = $this->getReturnPage('com_jongman.reservation.return_page', true);	
			if (!empty($returnPage)) {
				$this->setRedirect(JRoute::_($returnPage, false));
			}
		}
			
	}
	
	/** Not used */
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

		$recordId = JFactory::getApplication()->input->getInt($key);

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
		
		$return = $this->getReturnPage(null, true);
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
	
	public function save($key = null, $urlVar = null)
	{
		// Check for request forgeries.
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));
        $input = JFactory::getApplication()->input;
		// Initialise variables.
		$app   = JFactory::getApplication();
		$lang  = JFactory::getLanguage();
		$model = $this->getModel();
		$table = $model->getTable();
		$data  = $input->getVar('jform', array(), 'post', 'array');

		//$checkin = property_exists($table, 'checked_out');
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

		$recordId = $input->getInt($urlVar);

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

			$return = $this->getReturnPage(null, true);
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
        // return RFReservationSeries if validation passed
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
			//revert time to UTC
			$tz = RFApplicationHelper::getUserTimezone();
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
		
		$referenceNumber = $validData['series']->currentInstance()->referenceNumber();
		// Report success with reference number
		$this->setMessage(
			JText::sprintf('COM_JONGMAN_RESERVATION_SUCCESSFULLY_MADE', $referenceNumber)
		);

		// Clear the record id and data from the session.
		$this->releaseEditId($context, $recordId);
		$app->setUserState($context . '.data', null);
		
		// Redirect to the list screen.
		$return = $this->getReturnPage('com_jongman.reservation.return_page', true);
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
		$user = JFactory::getUser();
		$resourceId = JFactory::getApplication()->input->getInt('rid', null);
		
		$asset = ($resourceId === null) ? 'com_jongman' : 'com_jongman.resource.'.$resourceId;
		
		return $user->authorise('core.create', $asset);
	} 
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id') 
	{
		$app = JFactory::getApplication();
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);
		$input = $app->input;
		
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
		}else{
			$task = $this->getTask();
			$view = $input->getCmd('view');
			if ( ($task == 'add') && in_array($view, array('reservations', 'schedule')) ) {
				$schedule_id = $input->getCmd('id');
				$append .= '&schedule_id='.$schedule_id;
			}
		}
		
		if ($start_date !== null) {
			$append .= '&start='.$start_date;
		}
		
		if ($end_date !== null) {
			$append .= '&end='.$end_date;
		}
		
		/*
		if ($return !== null) {
			$append .= '&return='.base64_encode($return);
		}*/
		
		return $append;
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		
		$app = JFactory::getApplication();
		// from reservation view
		$schedule_id = $app->input->getInt('schedule_id', null);
		if ($schedule_id === null) {
			// from schedule view
			$schedule_id = $app->input->getInt('sid', null);
		}
		if (!empty($schedule_id)) {
			$append .= '&layout=calendar&id='.$schedule_id;	
		}
		
		return $append;
		
	}

}