<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  Site
 * @since       3.0.0
 */
class JongmanViewCustomer extends JViewLegacy
{
	/**
	 * @var    JObject	The data for the record being displayed.
	 * @since  3.0.0
	 */
	protected $item;

	/**
	 * @var    JForm  The form object for this record.
	 * @since  3.0.0
	 */
	protected $form;

	/**
	 * @var    JObject  The model state.
	 * @since  3.0.0
	 */
	protected $state;

	/**
	 * Prepare and display the Customer view.
	 *
	 * @return  void
	 * @since   3.0.0
	 */
	public function display($tpl = null)
	{
		// Intialiase variables.
		$app = JFactory::getApplication();
		
		if ($this->getLayout()=='postaction') {
			$this->postaction = $app->input->getWord('action', 'close');
			if ($this->postaction =='update') {
				$this->item		= $this->get('Item');
			}
				
		}else{
			$this->item		= $this->get('Item');
			$this->form		= $this->get('Form');
			$this->state	= $this->get('State');
			
		}
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->toolbar 	= $this->getToolbar();
		parent::display($tpl);
	}

	/**
	 * Get toolbar as HTML
	 *
	 * @return  void
	 * @since   3.0.0
	 */
	protected function getToolbar()
	{	
		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= JongmanHelper::getActions();

		$this->title = JText::_(
				'COM_JONGMAN_'.
				($checkedOut
					? 'VIEW_CUSTOMER'
					: ($isNew ? 'ADD_CUSTOMER' : 'EDIT_CUSTOMER')).'_TITLE'
			);
		
		if (!$checkedOut && ($canDo->get('core.edit') || $canDo->get('core.create'))) {
			RFToolbar::button('COM_JONGMAN_TOOLBAR_UPDATE', 'customer.save', false );
			RFToolbar::button('COM_JONGMAN_TOOLBAR_APPLY', 'customer.apply', false );
		}

		if (empty($this->item->id))  {			
			RFToolbar::button('COM_JONGMAN_TOOLBAR_CLOSE', 'customer.cancel', false);
		}
		else {
			RFToolbar::button('COM_JONGMAN_TOOLBAR_CANCEL', 'customer.cancel', false);
		}
		
		return RFToolbar::render();
	}
}