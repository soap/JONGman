<?php
/**
 * @version		$Id: view.html.php prasit.gebsaap $
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * View to edit a banner.
 *
 * @package		JONGman
 * @since		1.5
 */
class JongmanViewSchedule extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;
    
    function display($tpl = null) {
        JHtml::stylesheet( 'administrator/components/com_jongman/assets/css/jongman.css' );
  		// Initialiase variables.
		$this->form		= $this->get('Form');
		$this->item		= $this->get('Item');
		$this->state	= $this->get('State');
        
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

		JToolBarHelper::title($isNew ? JText::_('COM_JONGMAN_SCHEDULE_NEW_TITLE') : JText::_('COM_JONGMAN_SCHEDULE_EDIT_TITLE'), 'schedules.png');

		// If not checked out, can save the item.
		if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create'))) {
			JToolBarHelper::apply('schedule.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('schedule.save', 'JTOOLBAR_SAVE');

			if ($canDo->get('core.create')) {
				JToolBarHelper::custom('schedule.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
			}
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('schedule.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('schedule.cancel','JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('schedule.cancel', 'JTOOLBAR_CLOSE');
		}

		JToolBarHelper::divider();
	}
 }
