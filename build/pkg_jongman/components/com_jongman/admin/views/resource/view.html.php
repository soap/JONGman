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

class JongmanViewResource extends JViewLegacy 
{
    protected $form;
	protected $item;
	protected $state;
    
    function display($tpl = null) 
    {
        JHtml::stylesheet( 'administrator/components/com_jongman/assets/css/jongman.css' );
        
		JHtml::_('script', 'com_jongman/jquery/jquery-1.8.2.min.js', false, true);
		JHtml::_('script', 'com_jongman/jquery/jquery.noconflict.js', false, true);	
        
        $this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
        $this->canDo	= JongmanHelper::getActions();
        //hide advanced tab from display if no field exists
        if (count($this->form->getFieldset('advanced')) == 0) $this->ignore_fieldsets = array('advanced');
        
        $this->addToolbar();
        parent::display($tpl);
    }

   	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$userId		= $user->get('id');
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $userId);
		$canDo		= JongmanHelper::getActions();

		JToolBarHelper::title($isNew ? JText::_('COM_JONGMAN_RESOURCE_NEW_TITLE') : JText::_('COM_JONGMAN_RESOURCE_EDIT_TITLE'), 'resources.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create'))) {
			JToolBarHelper::apply('resource.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('resource.save', 'JTOOLBAR_SAVE');

			if ($canDo->get('core.create')) {
				JToolBarHelper::custom('resource.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('resource.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('resource.cancel','JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('resource.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
	}
    
}