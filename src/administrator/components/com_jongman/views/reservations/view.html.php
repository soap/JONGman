<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  Administrator
 * @since       1.0
 */
class JongmanViewReservations extends JViewLegacy
{
	/**
	 * @var    array  The array of records to display in the list.
	 * @since  1.0
	 */
	protected $items;

	/**
	 * @var    JPagination  The pagination object for the list.
	 * @since  1.0
	 */
	protected $pagination;

	/**
	 * @var    JObject	The model state.
	 * @since  1.0
	 */
	protected $state;
	protected $sidebar = null; 

	/**
	 * Prepare and display the reservations view.
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function display($tpl = null)
	{
		JHtml::stylesheet( 'administrator/components/com_jongman/assets/css/jongman.css' );
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->params     	= $this->state->get('params');
       	$this->owners		= $this->get('Owners');
       	
       	$this->workflow   	= ($this->params->get('approvalSystem')==2);
     
       	$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		if ($this->workflow) {
			$this->filterForm->removeField('state', 'filter');
		}
		$this->sidebar = JHtmlSidebar::render();	 
		
        $doc = JFactory::getDocument();
        if ($this->workflow) {
        	JHtml::_('jquery.ui');
        	jimport('workflow.framework');
        
        	$doc->addScript(JUri::root(true).'/media/com_workflow/workflow/js/statetransitions.js');
        	$doc->addScript(JUri::root(true).'/media/com_workflow/workflow/js/pnotify.custom.min.js');
        	$doc->addScript(JUri::root(true).'/media/com_workflow/workflow/js/jquery.blockUI.js');
        	$doc->addStyleSheet(JUri::root(true).'/media/com_workflow/workflow/css/pnotify.custom.min.css');
        }
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Add the toolbar and display the view layout.
		// TODO next version, complete reservation in back end or not provide it here
		$this->addToolbar();
		
		parent::display($tpl);

	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   1.0
	 */
	protected function addToolbar()
	{	
		// Initialise variables.
		$state	= $this->get('State');
		$canDo	= JongmanHelper::getActions();

		JToolBarHelper::title(JText::_('COM_JONGMAN_RESERVATIONS_TITLE'), 'reservations.png');
		
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('reservation.add', 'JTOOLBAR_NEW');
		}

		/*
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::checkin('reservations.checkin', 'JTOOLBAR_CHECKIN');
			JToolBarHelper::publishList('reservations.publish', 'JTOOLBAR_PUBLISH');
			JToolBarHelper::unpublishList('reservations.unpublish', 'JTOOLBAR_UNPUBLISH');

		}
		*/
		$repeatTypeFilter = $state->get('filter.repeat_type');
		if ($canDo->get('core.delete') && (!empty($repeatTypeFilter))) {
			if ($state->get('filter.repeat_type') == 'none') {
				JToolBarHelper::deleteList('', 'reservations.deletefull','JTOOLBAR_DELETE');
			}else{
				JToolBarHelper::deleteList('', 'reservations.deleteinstance','COM_JONGMAN_TOOLBAR_DELETE_THIS');
				JToolBarHelper::deleteList('', 'reservations.deletefull','COM_JONGMAN_TOOLBAR_DELETE_FULL');
				JToolBarHelper::deleteList('', 'reservations.deletefuture','COM_JONGMAN_TOOLBAR_DELETE_FUTURE');
			}
		} 

	}
	
	protected function getWorkflowStates()
	{
		jimport('workflow.framework');
		$wfStates = WFApplicationHelper::getStatesByContext('com_jongman.reservation');
		 
		return (empty($wfStates) ? false : $wfStates);
	}
}