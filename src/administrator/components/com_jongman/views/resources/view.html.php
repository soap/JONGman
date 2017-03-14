<?php
/**
 * @version     $Id$
 * @package     JONGman 2.0
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport ('joomla.application.component.view');

class JongmanViewResources extends JViewLegacy 
{
    /**
     * items to be displayed (this is a list view)
     */
    protected $items;
    /**
     * pagination for the items
     */
    protected $pagination;
    /**
     *
     * model 's state
     */
    protected $state;
    protected $sidebar = null;

    public  function display($tpl = null) 
    {
        JHtml::stylesheet( 'administrator/components/com_jongman/assets/css/jongman.css' );
        $layout = $this->getLayout();
        
        $this->items        = $this->get("Items");
        $this->pagination   = $this->get("Pagination");
        $this->state        = $this->get("State");
        $this->is_j25     	= version_compare(JVERSION, '3', 'lt');
        if (!$this->is_j25) {
        	$this->filterForm    = $this->get('FilterForm');
			$this->activeFilters = $this->get('ActiveFilters');
			if ($layout != 'modal') $this->sidebar = JHtmlSidebar::render();	
        } 
    	
        // Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
        
		$this->addToolbar();
        // Display the template
        parent::display($tpl);
    }

    protected function addToolbar() {
  		require_once JPATH_COMPONENT.'/helpers/jongman.php';
        JToolBarHelper::title(JText::_('COM_JONGMAN_RESOURCES_TITLE'), 'resources.png');

        $canDo	= JongmanHelper::getActions();
  		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('resource.add');
		}

		if (($canDo->get('core.edit'))) {
			JToolBarHelper::editList('resource.edit');
		}
		
        if (($canDo->get('core.edit.state'))) {
        	JToolBarHelper::divider();
        	JToolBarHelper::checkin('resources.checkin');
            JToolBarHelper::publishList('resources.publish');
            JToolBarHelper::unpublishList('resources.unpublish');
        }
        
        if (($canDo->get('core.delete'))) {
        	JToolBarHelper::divider();
			JToolBarHelper::deleteList('resources.delete');
		}
    }

}