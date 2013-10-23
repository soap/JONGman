<?php
/**
 * @@version $Id$
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Jongman view.
 *
 * @package     Joomla
 * @subpackage  JONGman
 * @since       1.0
 */
class JongmanViewQuotas extends JView
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
	 * Prepare and display the quotas view.
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function display()
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
		parent::display();
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

		JToolBarHelper::title(JText::_('COM_JONGMAN_QUOTAS_TITLE'), 'quotas.png');

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('quota.add', 'JTOOLBAR_NEW');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('quota.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::divider();
			JToolBarHelper::checkin('quotas.checkin');
			JToolBarHelper::publishList('quotas.publish', 'JTOOLBAR_PUBLISH');
			JToolBarHelper::unpublishList('quotas.unpublish', 'JTOOLBAR_UNPUBLISH');
		}

		if ($canDo->get('core.delete')) {
			JToolBarHelper::divider();
			JToolBarHelper::deleteList('', 'quotas.delete','JTOOLBAR_DELETE');
		} 

	}
}