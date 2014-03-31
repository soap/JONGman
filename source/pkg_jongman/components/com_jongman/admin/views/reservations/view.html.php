<?php
/**
 * @version     $Id$
 * @package     JONGman 2.0
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
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

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Add the toolbar and display the view layout.
		$this->addToolbar();
		parent::display($tpl);
		//var_dump($this);
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

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('reservation.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::checkin('reservations.checkin', 'JTOOLBAR_CHECKIN');
			JToolBarHelper::publishList('reservations.publish', 'JTOOLBAR_PUBLISH');
			JToolBarHelper::unpublishList('reservations.unpublish', 'JTOOLBAR_UNPUBLISH');

		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'reservations.delete','JTOOLBAR_DELETE');
		} 

	}
}