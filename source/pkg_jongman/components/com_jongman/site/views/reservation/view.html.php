<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once JPATH_COMPONENT.'/helpers/reservation.php';

/**
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  Frontend
 * @since       1.0
 */
class JongmanViewReservation extends JView
{
	/**
	 * @var    JObject	The data for the record being displayed.
	 * @since  1.0
	 */
	protected $item;

	/**
	 * @var    JForm  The form object for this record.
	 * @since  1.0
	 */
	protected $form;

	/**
	 * @var    JObject  The model state.
	 * @since  1.0
	 */
	protected $state;
	

	/**
	 * Prepare and display the Reservation view.
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function display()
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
		
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		
		$this->title = JText::_(
				'COM_JONGMAN_'.
				($checkedOut
					? 'VIEW_RESERVATION'
					: ($isNew ? 'ADD_RESERVATION' : 'EDIT_RESERVATION')).'_TITLE'
			);
			
		$this->canDelete = false;
		parent::display();
	}
}