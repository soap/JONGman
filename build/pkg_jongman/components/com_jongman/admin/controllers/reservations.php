<?php
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
	
	public function approve()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
	
		// Get items to publish from the request.
		$cid = JFactory::getApplication()->input->get('cid', array(), 'array');
		$data = array('approve' => 1, 'unapprove' => 0);
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