<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.controllerform');

class JongmanControllerInstance extends JControllerForm
{
	protected $view_list = 'schedule';
	
	protected $view_item = 'reservation';
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		$this->registerTask('updateinstance', 'save');
		$this->registerTask('updatefull', 'save');
		$this->registerTask('updatefuture', 'save');
		JFactory::getApplication()->input->set('layout', null);
	}
	
	protected function getRedirectToListAppend()
	{
		$append = parent::getRedirectToListAppend();
		
		$app = JFactory::getApplication();
		$schedule_id = $app->input->getInt('schedule_id', null);
		if (!empty($schedule_id)) {
			$append .= '&layout=calendar&id='.$schedule_id;	
		}
		
		return $append;	
	}	
	
}