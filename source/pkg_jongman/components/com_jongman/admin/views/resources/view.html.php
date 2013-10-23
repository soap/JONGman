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

class JongmanViewResources extends JView {
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

    public  function display($tpl = null) {
        JHtml::stylesheet( 'administrator/components/com_jongman/assets/css/jongman.css' );
        // Get data from the model
        $items              = $this->get('Items');
        $pagination         = $this->get('Pagination');
        // Assign data to the view
        $this->items        = $items;
        $this->pagination   = $pagination;
        $this->state        = $this->get('State');

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