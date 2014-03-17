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
		$this->toolbar	= $this->getToolbar();

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
		
		var_dump($this->item);
		
	}
	
	public function getToolbar()
	{
        $access  = JongmanHelper::getActions();
        $state   = $this->get('State');
        $item 	 = $this->get("Item");
        $isNew	 = $item->id == 0;
        $options = array();
		if ($isNew) {
        	RFToolbar::button(
            	'COM_JONGMAN_ACTION_SAVE',
            	'reservation.save',
            	false, //no need to select item first
           	 	array('access' => $access->get('core.edit'))
        	);
        	RFToolbar::button(
            	'JCANCEL',
            	'reservation.cancel',
             	false, //no need to select item first
           	 	array('acces' => true, 'icon'=>'icon-chevron-left')
        	);
		}else{
        
        	RFToolbar::button(
            	'COM_JONGMAN_ACTION_UPDATE_INSTANCE',
            	'instance.updateinstance',
           		false,
            	array('access' => $access->get('core.edit'),
            		'icon'=>'icon-ok')
        	);
        	
        	RFToolbar::button(
            	'COM_JONGMAN_ACTION_UPDATE_FULL',
            	'instance.updatefull',
           		false,
            	array('access' => $access->get('core.edit'),
            	'icon'=>'icon-ok')
        	);
        	
        	RFToolbar::button(
            	'COM_JONGMAN_ACTION_UPDATE_FUTURE',
            	'instance.updatefuture',
           		false,
            	array('access' => $access->get('core.edit'),
            	'icon'=>'icon-ok')
        	);
        	
        	RFToolbar::button(
            	'JCANCEL',
            	'instance.cancel',
             	false, //no need to select item first
           	 	array('acces' => true, 'icon'=>'icon-chevron-left')
        	);        	
		}



        return RFToolbar::render();	
	}
}