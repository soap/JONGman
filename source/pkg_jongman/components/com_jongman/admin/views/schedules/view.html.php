<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// import Joomla view library
jimport('joomla.application.component.view');

class JongmanViewSchedules extends JViewLegacy 
{
    /**
     * items to be displayed (this is a list view)
     */
    protected $items;
    /**
     * pagination for the items
     */
    protected $pagination;
    
    protected $is_j25;
    
    protected $state;
    /**
     * JongmanSchedules view display method
     * @return void
     */
    function display($tpl = null)
    {
        JHtml::stylesheet( 'administrator/components/com_jongman/assets/css/jongman.css' );
        // Assign data to the view
        $this->items        = $this->get('Items');
        $this->pagination   = $this->get('Pagination');
        $this->state        = $this->get('State');
        $this->is_j25     = version_compare(JVERSION, '3', 'lt');
        
        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
        $this->addToolbar();

        // Display the template
        parent::display($tpl);

    }
    
    function addToolbar() 
    {
  		require_once JPATH_COMPONENT .'/helpers/jongman.php';
        JToolBarHelper::title(JText::_('COM_JONGMAN_SCHEDULES_TITLE'), 'schedules.png');
        
        $canDo	= JongmanHelper::getActions();
  		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('schedule.add');
		}
        
		if (($canDo->get('core.edit'))) {
			JToolBarHelper::editList('schedule.edit');
		}
		
        if (($canDo->get('core.edit.state'))) {
        	JToolBarHelper::divider();
        	JToolBarHelper::checkin('schedules.checkin');
            JToolBarHelper::publishList('schedules.publish');
            JToolBarHelper::unpublishList('schedules.unpublish');
        }
        
		if ( $canDo->get('core.delete') ) {
			JToolBarHelper::divider();
			JToolBarHelper::deleteList('', 'schedules.delete','JTOOLBAR_DELETE');
		}         
    }
}
