<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
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
class JongmanControllerReservations extends JControllerAdmin
{
	public function __construct($config=array())
	{
		parent::__construct($config);

		$this->registerTask('unapprove', 'approve');
		$this->registerTask('approve', 'approve');
		$this->registerTask('deletefull', 'delete');
		$this->registerTask('deleteinstance', 'delete');
		$this->registerTask('deletefuture', 'delete');		
	}
	
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
	public function getModel($name = 'Reservation', $prefix = 'JongmanModel', $config = array())
	{
		return parent::getModel($name, $prefix, $config);
	}
	
	/**
	 * @todo override this
	 * @see JControllerAdmin::delete()
	 */
	public function delete()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	
		// Get items to remove from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
	
		if (!is_array($cid) || count($cid) < 1)
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			jimport('joomla.utilities.arrayhelper');
			JArrayHelper::toInteger($cid);
				
			switch ($this->getTask()) {
				case 'deleteinstance':
					$updatescope = 'this';
					break;
				case 'deletefull':
					$updatescope = 'full';
					break;
				case 'deletefuture':
					$updatescope = 'future';
					break;
				default:
					$updatescope = 'this';
					break;
			}
				
			$affected = 0;
			// Remove the items.
			if ($model->deleteById($cid[0], $updatescope, $affected))
			{
				$this->setMessage(JText::plural($this->text_prefix . '_N_ITEMS_DELETED', count($affected)));
			}
			else
			{
				$this->setMessage($model->getError(), 'error');
			}
	
			// Invoke the postDelete method to allow for the child class to access the model.
			$this->postDeleteHook($model, $cid);
		}
	
	
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list, false));
	}
	
	public function approve()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	
		// Get items to publish from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$data = array('approve' => 1, 'unapprove' => -1);
		$task = $this->getTask();
		$value = JArrayHelper::getValue($data, $task, 0, 'int');
	
		if (empty($cid))
		{
			JLog::add(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), JLog::WARNING, 'jerror');
		}
		else
		{
			// Get the model.
			$model = $this->getModel();
	
			// Make sure the item ids are integers
			JArrayHelper::toInteger($cid);
	
			// Publish the items.
			try
			{
				$approved = $model->approve($cid, $value);
				
				if ($value == 1)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_APPROVED';
				}
				elseif ($value == 0)
				{
					$ntext = $this->text_prefix . '_N_ITEMS_UNAPPROVED';
				}
	
				$this->setMessage(JText::plural($ntext, count($approved)));
			}
			catch (Exception $e)
			{
				$this->setMessage($e->getMessage(), 'error');
			}
		}
	
		$extension = $this->input->get('extension');
		$extensionURL = ($extension) ? '&extension=' . $extension : '';
		$this->setRedirect(JRoute::_('index.php?option=' . $this->option . '&view=' . $this->view_list . $extensionURL, false));
	}
	
}