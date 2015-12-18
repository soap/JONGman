<?php
defined('_JEXEC') or die;
/**
 * Controller for a single customer
 *
 */
class JongmanControllerCustomer extends JControllerForm
{
	
	public function add()
	{
		$input = JFactory::getApplication()->input;
		$input->set('id', null);
		parent::add();
	}
	
	public function save($key=null, $urlVar='id')
	{
		if (parent::save($key, $urlVar)) {
			
			if (($this->input->getInt('modal', 0) == 1) && ($this->getTask()=='save')) {
				$recordId = $this->input->getInt($urlVar);
				$this->input->set('layout', 'postaction');
				$this->setRedirect(
						JRoute::_(
								'index.php?option=' . $this->option . '&view=' . $this->view_item.'&modal=1&action=update'
								. $this->getRedirectToItemAppend($recordId, $urlVar), false
						)
				);
			}	
		}
	}

	public function cancel($key = null)
	{
		parent::cancel($key);

		if ($this->input->getInt('modal', 0) == 1) {
			$recordId = $this->input->getInt($urlVar);
			$this->input->set('layout', 'postaction');
			$this->setRedirect(
					JRoute::_(
							'index.php?option=' . $this->option . '&view=' . $this->view_item.'&modal=1&action=close'
							. $this->getRedirectToItemAppend($recordId, $key), false
					)
				);
		}
	}
	
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);

		$modal  = $this->input->get('modal', null);
		if ($modal) {
			$append .='&modal='.$modal;
		}

		return $append;
	}
}

