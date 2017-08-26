<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');
require_once JPATH_COMPONENT.'/helpers/reservation.php';

/**
 * Reservation view.
 *
 * @package     JONGman
 * @subpackage  Frontend
 * @since       1.0
 */
class JongmanViewReservation extends JViewLegacy
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
	public function display($tpl=NULL)
	{
		// Intialiase variables.
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');
		$this->toolbar	= $this->getToolbar();

		$this->customFields = count($this->form->getFieldsets('reservation_custom_fields')) > 0;
		$this->editorFields = $this->getEditorFields();
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
		
		$this->canDelete = (!$isNew);
		parent::display($tpl);
		
	}
	
	public function getToolbar()
	{
        $user	 = JFactory::getUser();
		$state   = $this->get('State');
        $item 	 = $this->get("Item");
        $isNew	 = $item->id == 0;
        $isRecurring = ($item->repeat_type !== 'none') || empty($item->repeat_type);
        
        $asset =  $isNew ? 'com_jongman' : 'com_jongman.resource.'.$this->item->resource_id;
        $isOwner = ($item->owner_id == $user->id) || ($item->created_by == $user->id);
        
        $access  = JongmanHelper::getActions($asset);
        
        $options = array();
		if ($isNew) {
        	RFToolbar::button(
            	'COM_JONGMAN_ACTION_SAVE',
            	'reservation.save',
            	false, //no need to select item first
           	 	array('access' => ($access->get('core.create') || $access->get('core.edit') || $access->get('core.edit.own') ))
        	);
        	RFToolbar::button(
            	'JCANCEL',
            	'reservation.cancel',
             	false, //no need to select item first
           	 	array('acces' => true, 'icon'=>'icon-chevron-left')
        	);
		}else {
			if ($isRecurring){
				$canEdit =  $access->get('core.edit') || ($access->get('core.edit.own') && $isOwner);
				$items = array();
				$items[] = array('text' => 'COM_JONGMAN_ACTION_UPDATE_INSTANCE', 'task' => 'instance.updateinstance', 'class'=>'');
				$items[] = array('text' => 'COM_JONGMAN_ACTION_UPDATE_FUTURE', 'task' => 'instance.updatefuture', 'class'=>'');
				$items[] = array('text' => 'COM_JONGMAN_ACTION_UPDATE_FULL', 'task' => 'instance.updatefull', 'class'=>'');
				RFToolbar::dropdownButton($items);
        		
        		$canDelete = $access->get('core.delete') || ($access->get('com_jongman.delete.own') && $isOwner);
        		
        		$items = array();
        		$options = array('class'=>'btn-info', 'icon'=>'icon-minus icon-white');
        		$items[] = array('text' => 'COM_JONGMAN_ACTION_DELETE_INSTANCE', 'task' => 'instance.deleteinstance');
        		$items[] = array('text' => 'COM_JONGMAN_ACTION_DELETE_FUTURE', 'task' => 'instance.deletefuture');
        		$items[] = array('text' => 'COM_JONGMAN_ACTION_DELETE_FULL', 'task' => 'instance.deletefull');
        		RFToolbar::dropdownButton($items, $options);

			}else{
				RFToolbar::button(
            		'COM_JONGMAN_ACTION_UPDATE',
            		'instance.updatefull',
           			false,
            		array('access' => $access->get('core.edit') ||  ($access->get('core.edit.own') && $isOwner),
            		'icon'=>'icon-ok')
        		);
				RFToolbar::button(
					'COM_JONGMAN_ACTION_DELETE',
					'instance.deletefull',
					false,
					array('access' => $access->get('core.delete') || ($access->get('com_jongman.delete.own') && $isOwner), 'icon' => 'icon-minus')
				);
			}
			
			RFToolbar::button(
				'JCANCEL',
				'instance.cancel',
				false, //no need to select item first
				array('acces' => true, 'icon'=>'icon-chevron-left')
			);
		}	
		
        return RFToolbar::render();	
	}
	
	protected function getEditorFields($form = null)
	{
		if ($form === null) $form = $this->get("Form");
		$result = array();
		
		$fieldSets = $form->getFieldsets('reservation_custom_fields');

		foreach($fieldSets as $fieldSet) {

			foreach ($form->getFieldset($fieldSet->name) as $field) {
				if ($field->type == 'Editor') {
					$result[] = $field;
				}
			}
		}
		
		return $result;
	}
}