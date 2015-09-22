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
					? 'VIEW_BLACKOUT'
					: ($isNew ? 'ADD_BLACKOUT' : 'EDIT_BLACKOUT')).'_TITLE'
			)
		);

		if ($isNew) {
			JToolBarHelper::save('blackout.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::apply('blackout.apply', 'JTOOLBAR_APPLY');
		}else{
			// If not checked out, can update the item.
			if (!$checkedOut && $canDo->get('core.edit')) {
				if ($this->item->repeat_type === 'none') {
					JToolBarHelper::apply('blackout.savethis', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('blackout.applythis', 'JTOOLBAR_SAVE');
				}else{
					JToolBarHelper::apply('blackout.savefull', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('blackout.applyfull', 'JTOOLBAR_SAVE');
				}

				//JToolBarHelper::custom('blackout.update2new', 'save-new.png', null, 'JTOOLBAR_SAVE_AND_NEW', false);
			}	
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('blackout.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('blackout.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}