<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * schedule list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jongman
 * @since		1.6
 */
class JongmanControllerSchedules extends JControllerAdmin
{
	public function __construct($config=array())
	{
		parent::__construct($config);
		$this->registerTask('unsetDefault',	'setDefault');
	}
	/**
	 * Proxy for getModel.
	 * @since	2.0
	 */
	public function getModel($name = 'Schedule', $prefix = 'JongmanModel', $config=array())
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}
	

	function setDefault()
	{
		// Check for request forgeries
		JSession::checkToken('request') or die(JText::_('JINVALID_TOKEN'));
	
		// Get items to publish from the request.
		$cid	= JRequest::getVar('cid', array(), '', 'array');
		$data	= array('setDefault' => 1, 'unsetDefault' => 0);
		$task 	= $this->getTask();
		$value	= JArrayHelper::getValue($data, $task, 0, 'int');
	
		if (empty($cid)) {
			JError::raiseWarning(500, JText::_($this->text_prefix.'_NO_ITEM_SELECTED'));
		} else {
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);
	
			// Publish the items.
			if (!$model->setDefault($cid, $value)) {
				JError::raiseWarning(500, $model->getError());
			} else {
				if ($value == 1) {
					$ntext = 'COM_JONGMAN_SCHEDULES_SET_DEFAULT';
				}
				else {
					$ntext = 'COM_JONGMAN_SCHEDULES_UNSET_DEFAULT';
				}
				$this->setMessage(JText::plural($ntext, count($cid)));
			}
		}
	
		$this->setRedirect(JRoute::_('index.php?option='.$this->option.'&view='.$this->view_list, false));
	}	

}