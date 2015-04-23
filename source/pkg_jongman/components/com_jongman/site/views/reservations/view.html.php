<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  Site
 * @since       3.0.0
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
		$this->params     	= $this->state->get('params');
		$this->owners		= $this->get('Owners');

		$this->workflow   	= ($this->params->get('approvalSystem')==2);
		$doc = JFactory::getDocument();
		if ($this->workflow) {
			JHtml::_('jquery.ui');
			jimport('workflow.framework');

			$doc->addScript(JUri::root(true).'/media/com_workflow/workflow/js/statetransitions.js');
			$doc->addScript(JUri::root(true).'/media/com_workflow/workflow/js/pnotify.custom.min.js');
			$doc->addScript(JUri::root(true).'/media/com_workflow/workflow/js/jquery.blockUI.js');
			$doc->addStyleSheet(JUri::root(true).'/media/com_workflow/workflow/css/pnotify.custom.min.css');
		}
		$doc->addStyleDeclaration(JUri::root(true).'/media/com_jongman/jongman/css/styles.css');
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		
		$this->toolbar       = $this->getToolbar();
		$this->sort_options  = $this->getSortOptions();
		$this->order_options = $this->getOrderOptions();
		
		// Escape strings for HTML output
		$this->pageclass_sfx = htmlspecialchars($this->params->get('pageclass_sfx'));
		
		// Check for empty filter result
		if ((count($this->items) == 0) && $this->state->get('filter.isset')) {
			$app->enqueueMessage(JText::_('COM_JONGMAN_EMPTY_SEARCH_RESULT'));
		}
		
		// Check for layout override
		if (isset($active->query['layout']) && (JRequest::getCmd('layout') == '')) {
			$this->setLayout($active->query['layout']);
		}
		
		// Display the view layout.
		parent::display($tp);
	}

    /**
     * Generates the toolbar for the top of the view
     *
     * @return    string    Toolbar with buttons
     */
    protected function getToolbar()
    {
        $access = JongmanHelper::getActions();
        $state  = $this->get('State');

        RFToolbar::filterButton($this->state->get('filter.isset'));

        return RFToolbar::render();
    }
	
	protected function getSortOptions()
	{
		
	}
	
	protected function getOrderOptions()
	{
		
	}
}