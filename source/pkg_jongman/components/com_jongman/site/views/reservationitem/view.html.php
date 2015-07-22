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
		$this->item 		= $this->get("Item");
		$this->form			= $this->get("Form");
		$this->state 		= $this->get("State");
		$this->params 		= $this->state->get("params");
		$this->logs			= $this->get("Logs");
		
		$this->customFields = count($this->form->getFieldsets('reservation_custom_fields')) > 0;
		
		if ($this->params->get('approvalSystem')==2) {
			jimport('workflow.framework');
			$this->transitions	= $this->get("Transitions");
			$this->workflowToolbar 	= $this->getWorkflowToolbar();
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		$app = JFactory::getApplication();
		$this->print 		= $app->input->getBool('print');
		$this->toolbar		= $this->getToolbar();
		
		if ($this->print) {
			$this->_prepareDocument();
		}
		parent::display($tpl);
	}
	
	protected function getToolbar()
	{
		$access = JongmanHelper::getActions('com_jongman.reservation');

		$doc = JFactory::getDocument();
		$doc->addStyleDeclaration(JUri::root(true).'/media/com_jongman/jongman/css/styles.css');
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
	
	protected function _prepareDocument()
	{
		JHtml::_('stylesheet', 'com_jongman/jongman/report.css', false, true, false, false, false);
		if ($this->print)
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
		}
	}
}