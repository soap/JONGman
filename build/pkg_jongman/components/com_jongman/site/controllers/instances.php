<?php

defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

class JongmanControllerInstances extends JControllerAdmin
{
	protected $view_list = 'reservations';
	
	public function __construct($config) 
	{
		parent::__construct($config);
		
		$this->registerTask('deleteinstance', 'delete');
		$this->registerTask('deletefull', 'delete');
		$this->registerTask('deletefuture', 'delete');		
	}
	
	public function getModel($name = 'Instance', $prefix = 'JongmanModel', $config = array('ignore_request' => true))
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
	
	protected function getRedirectToListAppend()
	{
		$app = JFactory::getApplication();
		$append = '&layout=calendar';
		
		$schedule_id = $app->input->getInt('schedule_id', null);
		if (!empty($schedule_id)) {
			$append .= '&id='.$schedule_id;
		}
	
		return $append;
	
	}	
}