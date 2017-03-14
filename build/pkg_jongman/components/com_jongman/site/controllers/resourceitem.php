<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

class JongmanControllerResourceitem extends JControllerForm
{
	protected $view_list = 'resources';
	
	protected $view_item = 'resourceitem';
	
	public function view()
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$table = $model->getTable();
		$cid = JRequest::getVar('cid', array(), 'get', 'array');
		$context = "$this->option.view.$this->context";

		// Determine the name of the primary key for the data.
		if (empty($key))
		{
			$key = $table->getKeyName();
		}

		// To avoid data collisions the urlVar may be different from the primary key.
		if (empty($urlVar))
		{
			$urlVar = $key;
		}

		// Get the previous record id (if any) and the current record id.
		$recordId = (int) (count($cid) ? $cid[0] : JRequest::getInt($urlVar));

		// Access check.
		if (!$this->allowView(array($key => $recordId), $key))
		{
			$this->setError(JText::_('COM_JONGMAN_ERROR_VIEW_NOT_PERMITTED'));
			$this->setMessage($this->getError(), 'error');

			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . '&view=' . $this->view_list
					. $this->getRedirectToListAppend(), false
				)
			);

			return false;
		}

		// Check-out succeeded, push the new record id into the session.
		$this->holdEditId($context, $recordId);
		$app->setUserState($context . '.data', null);

		$this->setRedirect(
			JRoute::_(
				'index.php?option=' . $this->option . '&view=' . $this->view_item
				. $this->getRedirectToItemAppend($recordId, $urlVar), false
			)
		);

		return true;		
	}
	
	protected function allowView($data = array(), $key = 'id')
	{
		return true;	
	}
}