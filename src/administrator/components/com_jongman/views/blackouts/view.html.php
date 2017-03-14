<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  admin
 * @since       3.0
 */
class JongmanViewBlackouts extends JViewLegacy
{
	/**
	 * @var    array  The array of records to display in the list.
	 * @since  3.0
	 */
	protected $items;

	/**
	 * @var    JPagination  The pagination object for the list.
	 * @since  3.0
	 */
	protected $pagination;

	/**
	 * @var    JObject	The model state.
	 * @since  3.0
	 */
	protected $state;

	/**
	 * Prepare and display the Blackouts view.
	 *
	 * @return  void
	 * @since   3.0
	 */
	public function display($tp = NULL)
	{
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->sidebar = JHtmlSidebar::render();
		
		// Add the toolbar if it is not in modal
		if ($this->getLayout() !== 'modal') $this->addToolbar();
		
		// Display the view layout.
		parent::display();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   3.0
	 */
	protected function addToolbar()
	{
		// Initialise variables.
		$state	= $this->get('State');
		$canDo	= JongmanHelper::getActions();

		JToolBarHelper::title(JText::_('COM_JONGMAN_BLACKOUTS_TITLE'));

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('blackout.add', 'JTOOLBAR_NEW');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('blackout.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publishList('blackout.publish', 'JTOOLBAR_PUBLISH');
			JToolBarHelper::unpublishList('blackout.unpublish', 'JTOOLBAR_UNPUBLISH');
			JToolBarHelper::archiveList('blackout.archive','JTOOLBAR_ARCHIVE');
		}

		if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'blackouts.delete','JTOOLBAR_EMPTY_TRASH');
		} 
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('blackouts.trash','JTOOLBAR_TRASH');
		}

	}
}