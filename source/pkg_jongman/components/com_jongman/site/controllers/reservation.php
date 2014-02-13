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
		$input = JFactory::getApplication()->input;
		$input->set('layout', 'edit');
		return parent::edit($key, $urlVar);
		
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