<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
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
class JongmanControllerResources extends JControllerAdmin
{
	public function __construct($config=array())
	{
		parent::__construct($config);
		$this->registerTask('setapproval', 'approval');
		$this->registerTask('resetapproval', 'approval');	
	}
	
	
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the model class name.
	 * @param   string  $config  The model configuration array.
	 *
	 * @return  JongmanModelResource	The model for the controller set to ignore the request.
	 * @since   1.6
	 */
	public function getModel($name = 'Resource', $prefix = 'JongmanModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
	
	public function approval()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));

        $request = JFactory::getApplication()->input;

        // Get items to publish from the request.
        $cid = $request->get('cid', array(), 'array');
		//$cid = JRequest::getVar('cid', array(), '', 'array');
		$data = array('setapproval' => 1, 'resetapproval' => 0);
		$task = $this->getTask();

		$value = JArrayHelper::getValue($data, $task, 0, 'int');

		if (empty($cid))
		{
			JError::raiseWarning(500, JText::_($this->text_prefix . '_NO_ITEM_SELECTED'));
		}
		else
		{
			// Get the model.
			$model = $this->getModel();

			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);

			// Publish the items.
			if (!$model->approval($cid, $value))
			{
				JError::raiseWarning(500, $model->getError());
			}
			else
			{
				if ($value == 1)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_APPROVAL_SET';
				}
				elseif ($value == 0)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_APPROVAL_RESET';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}
		$extension = $request->getCmd('extension');
		$extensionURL = ($extension) ? '&extension=' . $request->getCmd('extension') : '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}
	
}