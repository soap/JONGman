<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Blackout view.
 *
 * @package     JONGman
 * @subpackage  Admin
 * @since       1.0.0
 */
class JongmanViewBlackout extends JViewLegacy
{
	protected $item;
	protected $form;
	protected $state;

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
		
		JHtml::_('script', 'com_jongman/jongman/reservation.js', false, true);
		JHtml::_('script', 'com_jongman/jongman/date-helper.js', false, true);
		JHtml::_('script', 'com_jongman/jongman/recurrence.js', false, true);
		$this->addToolbar();
		
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   1.0
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
					? 'VIEW_Blackout'
					: ($isNew ? 'ADD_BLACKOUT' : 'EDIT_BLACKOUT')).'_TITLE'
			)
		);

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')) {
			JToolBarHelper::apply('blackout.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('blackout.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('blackout.save2new', 'save-new.png', null, 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('blackout.save2copy', 'save-copy.png', null, 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('blackout.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('blackout.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}