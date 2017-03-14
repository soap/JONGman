<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

/**
 * Blackout Subcontroller.
 *
 * @package     JONGman
 * @subpackage  Admin
 * @since       3.0
 */
class JongmanControllerBlackout extends JControllerForm
{
	protected $context = 'blackout';
	
	public function save($key = null, $urlVar = null) {
		// Check for request forgeries.
		JSession::checkToken () or jexit ( JText::_ ( 'JINVALID_TOKEN' ) );
		
		// Initialise variables.
		$app = JFactory::getApplication ();
		$lang = JFactory::getLanguage ();
		$model = $this->getModel ();
		$table = $model->getTable ();
		$data = JRequest::getVar ( 'jform', array (), 'post', 'array' );
		$checkin = property_exists ( $table, 'checked_out' );
		$context = "$this->option.edit.$this->context";
		$task = $this->getTask ();
		
		// Determine the name of the primary key for the data.
		if (empty ( $key )) {
			$key = $table->getKeyName ();
		}
		
		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty ( $urlVar )) {
			$urlVar = $key;
		}
		
		$recordId = $app->input->getInt ( $urlVar );
		
		if (! $this->checkEditId ( $context, $recordId )) {
			// Somehow the person just went to the form and tried to save it. We don't allow that.
			$this->setError ( JText::sprintf ( 'JLIB_APPLICATION_ERROR_UNHELD_ID', $recordId ) );
			$this->setMessage ( $this->getError (), 'error' );
			
			$this->setRedirect ( JRoute::_ ( 'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend (), false ) );
			
			return false;
		}
		
		// Populate the row id from the session.
		$data [$key] = $recordId;
		
		// Access check.
		if (! $this->allowSave ( $data, $key )) {
			$this->setError ( JText::_ ( 'JLIB_APPLICATION_ERROR_SAVE_NOT_PERMITTED' ) );
			$this->setMessage ( $this->getError (), 'error' );
			
			$this->setRedirect ( JRoute::_ ( 'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend (), false ) );
			
			return false;
		}
		
		// Validate the posted data.
		// Sometimes the form needs some posted data, such as for plugins and modules.
		$form = $model->getForm ( $data, false );
		
		if (! $form) {
			$app->enqueueMessage ( $model->getError (), 'error' );
			
			return false;
		}
		
		// Test whether the data is valid.
		$validData = $model->validate ( $form, $data );
		
		// Check for validation errors.
		if ($validData === false) {
			// Get the validation messages.
			$errors = $model->getErrors ();
			
			// Push up to three validation messages out to the user.
			for($i = 0, $n = count ( $errors ); $i < $n && $i < 3; $i ++) {
				if ($errors [$i] instanceof Exception) {
					$app->enqueueMessage ( $errors [$i]->getMessage (), 'warning' );
				} else {
					$app->enqueueMessage ( $errors [$i], 'warning' );
				}
			}
			// revert time to UTC
			$tz = JongmanHelper::getUserTimezone ();
			
			$data ['start_time']	= JDate::getInstance ( $data ['start_time'], $tz )->format ( 'H:i:s', false );
			$data ['end_time'] 		= JDate::getInstance ( $data ['end_time'], $tz )->format ( 'H:i:s', false );
			
			// Save the data in the session.
			$app->setUserState ( $context . '.data', $data );
			
			// Redirect back to the edit screen.
			$this->setRedirect ( JRoute::_ ( 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend ( $recordId, $urlVar ), false ) );
			
			return false;
		}
		
		if (!$model->save ( $validData )) {
			// Save the data in the session.
			$app->setUserState ( $context . '.data', $validData );
			
			// Redirect back to the edit screen.
			$this->setError ( JText::sprintf ( 'JLIB_APPLICATION_ERROR_SAVE_FAILED', $model->getError () ) );
			$this->setMessage ( $this->getError (), 'error' );
			
			$this->setRedirect ( JRoute::_ ( 'index.php?option=' . $this->option . '&view=' . $this->view_item . $this->getRedirectToItemAppend ( $recordId, $urlVar ), false ) );
			
			return false;
		}
		
		// Report success with reference number
		$this->setMessage ( JText::_( 'COM_JONGMAN_BLACKOUT_SUCCESSFULLY_MADE'));
		
		// Clear the record id and data from the session.
		$this->releaseEditId ( $context, $recordId );
		$app->setUserState ( $context . '.data', null );
		
		// Redirect to the list screen.
		$this->setRedirect ( JRoute::_ ( 'index.php?option=' . $this->option . '&view=' . $this->view_list . $this->getRedirectToListAppend (), false ) );
		
		// Invoke the postSave method to allow for the child class to access the model.
		$this->postSaveHook ( $model, $validData );
		
		return true;
	}	
}