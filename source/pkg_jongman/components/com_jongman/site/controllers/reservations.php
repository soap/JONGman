<?php
/**
 * @version: $Id$
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Reservations Subcontroller.
 *
 * @package     JONGman
 * @subpackage  Admin
 * @since       1.0
 */
class JongmanControllerReservations extends JControllerAdmin
{
	protected $text_prefix = "COM_JONGMAN_RESERVATIONS"; 
	/**
	 * Proxy for getModel.
	 * 
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the model class name.
	 * @param   string  $config  The model configuration array.
	 *
	 * @return  JongmanModelReservations	The model for the controller set to ignore the request.
	 * @since   1.6
	 */
	public function getModel($name = 'Reservation', $prefix = 'JongmanModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
	
	function canDelete($record) 
	{
		require_once JPATH_COMPONENT.'/helpers/reservation.php';
		return ReservationHelper::canDelete($record);	
	}
	
	function delete() 
	{
		$id = JRequest::getInt('id');
		JRequest::setVar('cid', $id);

		parent::delete();
		
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=closepopup&refresh=1', false));
	}
}