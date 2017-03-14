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
class JongmanViewCustomers extends JViewLegacy
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

		JToolBarHelper::title(JText::_('COM_JONGMAN_CUSTOMERS_TITLE'));

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('customer.add', 'JTOOLBAR_NEW');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('customer.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publishList('customer.publish', 'JTOOLBAR_PUBLISH');
			JToolBarHelper::unpublishList('customer.unpublish', 'JTOOLBAR_UNPUBLISH');
			JToolBarHelper::archiveList('customer.archive','JTOOLBAR_ARCHIVE');
		}

		if ($state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'customers.delete','JTOOLBAR_EMPTY_TRASH');
		}
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('customers.trash','JTOOLBAR_TRASH');
		}

	}
}