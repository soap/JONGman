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
		$this->toolbar = $this->getToolbar();
		parent::display();
	}
	
  	function getToolbar() 
  	{
    	// add required stylesheets from admin template
        $document    = & JFactory::getDocument();
        $document->addStyleSheet('administrator/templates/system/css/system.css');
        $document->addStyleSheet('media/com_jongman/css/toolbar.css');
        //now we add the necessary stylesheets from the administrator template
        //in this case i make reference to the bluestork default administrator template in joomla 1.6
       /*
        
        $document->addCustomTag(
				'<link href="administrator/templates/bluestork/css/template.css" rel="stylesheet" type="text/css" />'."\n\n".
                '<!--[if IE 7]>'."\n".
                '<link href="administrator/templates/bluestork/css/ie7.css" rel="stylesheet" type="text/css" />'."\n".
                '<![endif]-->'."\n".
                '<!--[if gte IE 8]>'."\n\n".
                '<link href="administrator/templates/bluestork/css/ie8.css" rel="stylesheet" type="text/css" />'."\n".
                '<![endif]-->'."\n".
                '<link rel="stylesheet" href="administrator/templates/bluestork/css/rounded.css" type="text/css" />'."\n"
              );
        */
        //load the JToolBar library and create a toolbar
        jimport('joomla.html.toolbar');
        $bar = new JToolBar( 'toolbar' );
        //and make whatever calls you require
        $bar->appendButton( 'Standard', 'save', 'Save', 'reservation.save', false );
        if (ReservationHelper::candelete($this->item)) {
        	$bar->appendButton( 'Standard', 'delete', 'Delete', 'reservations.delete', false );	
        }
        $bar->appendButton( 'Separator' );
        $bar->appendButton( 'Standard', 'cancel', 'Cancel', 'reservation.cancel', false );
        //generate the html and return
        return $bar->render();
	}
}