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
	
	public function edit($key = null, $urlVar = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$table = $model->getTable();
		$cid = $app->input->getVar('cid', array());
		$context = "$this->option.edit.$this->context";

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
		$recordId = (int) (count($cid) ? $cid[0] : $app->input->getInt($urlVar));
		$checkin = property_exists($table, 'checked_out');

		// Access check.
		if (!$this->allowEdit(array($key => $recordId), $key))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_EDIT_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Attempt to check-out the new record for editing and redirect.
		if ($checkin && !$model->checkout($recordId))
		{
			// Check-out failed, display a notice but allow the user to see the record.
			$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_CHECKOUT_FAILED', $model->getError()));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return false;
		}
		else
		{
			// Check-out succeeded, push the new record id into the session.
			$this->holdEditId($context, $recordId);
			$app->setUserState($context . '.data', null);

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_item
					. $this->getRedirectToItemAppend($recordId, $urlVar), false
				)
			);

			return true;
		}
	}
	
	/**
	 * Override to add support to redirect page with parameter
	 * @see JControllerForm::cancel()
	 */
	public function cancel($key = null)
	{
		parent::cancel($key);
		
		$this->setRedirect($this->getReturnPage());
	}
	
	public function save() 
	{
		if (parent::save()) { 
			$this->setRedirect($this->getReturnPage());
		}
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
		return ReservationHelper::canEdit($data, $key);
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
		$schedule_id = $app->input->getInt('sid', null);
		if (empty($schedule_id)) {
			$schedule_id = $app->input->getInt('schedule_id', null);
		}
		
		return $append.'&layout=calendar&id='.$schedule_id;
		
	}
	
	protected function getReturnPage()
	{
		$return = JRequest::getVar('return', null, 'default', 'base64');

        return base64_decode($return);
	}
}