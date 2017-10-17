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
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanViewLayouts extends JViewLegacy
{
	/**
	 * @var    array  The array of records to display in the list.
	 * @since  2.0
	 */
	protected $items;

	/**
	 * @var    JPagination  The pagination object for the list.
	 * @since  2.0
	 */
	protected $pagination;

	/**
	 * @var    JObject	The model state.
	 * @since  2.0
	 */
	protected $state;
	
	protected $sidebar = null;

	/**
	 * Prepare and display the Layouts view.
	 *
	 * @return  void
	 * @since   2.0
	 */
	public function display($tp = NULL)
	{
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
        $this->is_j25     	= version_compare(JVERSION, '3', 'lt');
        if (!$this->is_j25) {
        	$this->filterForm    = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');
			$this->sidebar = JHtmlSidebar::render();	
        } 
        
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Add the toolbar if it is not in modal
		if ($this->getLayout() !== 'modal') $this->addToolbar();
		
		// Display the view layout.
		parent::display();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   2.0
	 */
	protected function addToolbar()
	{
		// Initialise variables.
		$state	= $this->get('State');
		$canDo	= JongmanHelper::getActions();
		JToolBarHelper::title(JText::_('COM_JONGMAN_LAYOUTS_TITLE'));

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('layout.add', 'JTOOLBAR_NEW');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('layout.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publishList('layouts.publish', 'JTOOLBAR_PUBLISH');
			JToolBarHelper::unpublishList('layouts.unpublish', 'JTOOLBAR_UNPUBLISH');
		}

		if ($state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'layouts.delete','JTOOLBAR_EMPTY_TRASH');
		} 
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('layouts.trash','JTOOLBAR_TRASH');
		}

	}
}