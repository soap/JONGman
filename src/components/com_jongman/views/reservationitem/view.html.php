<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Reservationitem view.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       3.0
 */
class JongmanViewReservationitem extends JViewLegacy
{
	public function display($tpl = null)
	{
		jimport('workflow.framework');
		$app = JFactory::getApplication();
		
		$this->item 		= $this->get("Item");
		$this->form			= $this->get("Form");
		$this->state 		= $this->get("State");
		$this->params 		= $this->state->get("params");
		$this->logs			= $this->get("Logs");
		$this->transitions	= $this->get("Transitions");
		$this->customFields = count($this->form->getFieldsets('reservation_custom_fields')) > 0;
		$this->format		= 'html';
		
		$this->workflowToolbar 	= $this->getWorkflowToolbar();
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->print 		= $app->input->getBool('print');
		$this->toolbar		= $this->getToolbar();
		
		if ($this->print) {
			$this->_prepareDocument();
		}
		parent::display($tpl);
	}
	
	/**
	 * @internal $this->item->params 's access-[permission] properties were set by model
	 * @return string
	 */
	protected function getToolbar()
	{
		$doc = JFactory::getDocument();
		//$doc->addStyleDeclaration(JUri::root(true).'/media/com_jongman/jongman/css/styles.css');
		$items = array();
		
		$items[] = array(
				'text' => 'COM_JONGMAN_ACTION_CANCEL',
				'task' => 'reservationitem.cancel',
				'options' => array()
		);
		
		$items[] = array(
				'text' => 'COM_JONGMAN_ACTION_EDIT',
				'task' => 'instance.edit',
				'options' => array('access' => $this->item->params->get('access-edit'))
		);
		/*	
		$items[] = array(
				'text' => 'COM_JONGMAN_ACTION_DELETE',
				'task' => 'instance.delete',
				'options' => array('access' => $this->item->params->get('access-delete'))
		);
		*/
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
		if ($this->print || $this->format == 'pdf')
		{
			$this->document->setMetaData('robots', 'noindex, nofollow');
			if ($signature = $this->getSignature($this->item->owner_id)) {
					$this->ownerSignature = JPath::clean(trim(stripslashes($signature), '"'), '/'); 	
			}else{
				$this->ownerSignature = false;
			}
			
			$this->approverSignature = false;
			$this->ackbySignature = false;
			
			if (isset($this->item->attribs) && $this->item->attribs instanceof JRegistry) {
				$approved_by = $this->item->attribs->get('approved_by', 0);
				if ($approved_by) {
					$this->item->approver_id = $approved_by;
					if ($signature = $this->getSignature($approved_by)) {
						$this->approverSignature = JPath::clean(trim(stripslashes($signature), '"'), '/');
					}
				}else{
					$this->item->approver_id = 0;
				}
			}
			
			if (isset($this->item->attribs) && $this->item->attribs instanceof JRegistry) {
				$acked_by = $this->item->attribs->get('acked_by', 0);
				if ($acked_by) {
					$this->item->ackby_id = $acked_by;
					if ($signature = $this->getSignature($acked_by)) {
						$this->ackbySignature = JPath::clean(trim(stripslashes($signature), '"'), '/');
					}
				}else{
					$this->item->ackby_id = 0;
				}
			}
			
		}else{
			$this->ownerSignature = false;
			$this->approverSignature = false;
			$this->ackbySignature = false;
		}
	}
	
	private function getSignature($userId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('profile_key, profile_value')
			->from('#__user_profiles')
			->where('profile_key = '.$db->quote('bizprofile.signature'))
			->where('user_id='.(int)$userId)
			->order('ordering DESC');
		
		$db->setQuery($query);
		$result = $db->loadObject();
		
		if ($result && isset($result->profile_value)) {
			return $result->profile_value;
		}
		
		return false;
	}
}