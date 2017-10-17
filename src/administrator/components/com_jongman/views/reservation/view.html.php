<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  Admin
 * @since       2.0
 */
class JongmanViewReservation extends JViewLegacy
{
	/**
	 * @var    JObject	The data for the record being displayed.
	 * @since  2.0
	 */
	protected $item;

	/**
	 * @var    JForm  The form object for this record.
	 * @since  2.0
	 */
	protected $form;

	/**
	 * @var    JObject  The model state.
	 * @since  2.0
	 */
	protected $state;

	/**
	 * Prepare and display the Reservation view.
	 *
	 * @return  void
	 * @since   2.0
	 */
	public function display($tpl = null)
	{
		// Intialiase variables.
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		$jsUrl = JUri::root(true).'/media/com_jongman/js/reservation.js';
		JFactory::getDocument()->addScript($jsUrl);
		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   2.0
	 */
	protected function addToolbar()
	{
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= JongmanHelper::getActions();

		JToolBarHelper::title(
			JText::_(
				'COM_JONGMAN_'.
				($checkedOut
					? 'VIEW_RESERVATION'
					: ($isNew ? 'ADD_RESERVATION' : 'EDIT_RESERVATION')).'_TITLE'
			)
		);

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')) {
			JToolBarHelper::apply('reservation.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('reservation.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('reservation.save2new', 'save-new.png', null, 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('reservation.save2copy', 'save-copy.png', null, 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('reservation.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('reservation.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}