<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Reservationitem view.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanViewReservationitem extends JViewLegacy
{
	public function display($tpl = null)
	{
		jimport('workflow.framework');
		
		$this->item 		= $this->get("Item");
		$this->state 		= $this->get("State");
		$this->params 		= $this->state->get("params");
		$this->logs			= $this->get("Logs");
		$this->transitions	= $this->get("Transitions");
		
		$this->workflowToolbar 	= $this->getWorkflowToolbar();
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->toolbar		= $this->getToolbar();
		
		parent::display($tpl);
	}
	
	protected function getToolbar()
	{
		$access = JongmanHelper::getActions('com_jongman.reservation');
			
		$items = array();
		$items[] = array(
				'text' => 'COM_JONGMAN_ACTION_DELETE',
				'task' => 'instance.delete',
				'options' => array('access' => $access->get('core.delete'))
		);
		
		$items[] = array(
				'text' => 'COM_JONGMAN_ACTION_CANCEL',
				'task' => 'reservationitem.cancel',
				'options' => array()
		);
		
		RFToolbar::dropdownButton($items);
		//if (count($items)) {
		//	RFToolbar::listButton($items);
		//}
		
		//RFToolbar::filterButton($this->state->get('filter.isset'));
		
		return RFToolbar::render();		
	}
	
	protected function getWorkflowToolbar()
	{
		if (count($this->transitions) == 0) return '';
		$options = array();
		foreach($this->transitions as $transition) {
			WFToolbar::workflowButton($transition->title, 'reservationitem.transition', $transition, $options);
		}
		 
		return WFToolbar::render();
	}
}