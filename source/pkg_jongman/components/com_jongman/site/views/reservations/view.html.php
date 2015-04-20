<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  Site
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
	 * Prepare and display the Reservations view.
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function display($tp = NULL)
	{
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		// Display the view layout.
		parent::display($tp);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   1.0
	 */
	protected function addToolbar()
	{
	
	}
}