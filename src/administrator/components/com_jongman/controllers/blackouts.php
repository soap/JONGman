<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controlleradmin');

/**
 * Blackouts Subcontroller.
 *
 * @package     JONGman
 * @subpackage  Admin
 * @since       3.0
 */
class JongmanControllerBlackouts extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * 
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the model class name.
	 * @param   string  $config  The model configuration array.
	 *
	 * @return  JongmanModelBlackouts	The model for the controller set to ignore the request.
	 * @since   1.6
	 */
	public function getModel($name = 'Blackout', $prefix = 'JongmanModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}