<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

/**
 * Reservationitem Subcontroller.
 *
 * @package     JONGman
 * @subpackage  Site
 * @since       1.0
 */
class JongmanControllerReservationitem extends JControllerForm
{
	protected $view_list = 'reservations';
	
	public function cancel($key=NULL)
	{
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
		return true;		
	}
	
	public function transition($key=null)
	{
  		// Initialise variables.
		$app = JFactory::getApplication();
		$id = $app->input->getInt('id', null);
		$transitionId = $app->input->getInt('transition_id', null);
		$comment = $app->input->get('comment', '', 'string');
		$context = "$this->option.$this->context";
		
		$data = array('id' => $id, 'transition_id'=>$transitionId, 'table'=>$table);
		if (!$this->allowTransition($data, $key))
		{
			$this->setError(JText::_('COM_WORKFLOW_APPLICATION_ERROR_TRANSITION_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(str_replace('&amp;','&',$this->getReturnPage()), false)
			);	

			return false;
		}
		
		jimport('workflow.framework');
		jimport('workflow.application.helper');
		
		//WFApplicationHelper::makeTransition('com_jongman.reservation', $item_id, $transitionId, $comment)
		if (!WFApplicationHelper::makeTransition('com_jongman.reservation', $id, $transitionId, $comment))
		{
			$this->setRedirect(
				JRoute::_(str_replace('&amp;','&',$this->getReturnPage()), false)
			);	
		}
		
		$params = JComponentHelper::getParams('com_jongman');
		$page = $params->get('workflow_return_page', '0');
		if ($page == '0') {
			$this->setRedirect(
				JRoute::_('index.php?option=com_jongman&view='.$this->view_list.
					$this->getRedirectToListAppend(), false)
			);	
		}else {
			$this->setRedirect(
				JRoute::_(str_replace('&amp;', '&', $this->getReturnPage()), false)
			);
		}	
		
		return true;
	}
	
	protected function allowTransition($data)
	{
		return true;
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