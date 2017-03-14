<?php
defined('_JEXEC') or die;

jimport('jongman.controller.admin.json');

class JongmanControllerTimeslot extends JMControllerAdminJson
{
	public function layout()
	{
		$layout_id = JFactory::getApplication()->input->getInt('layout_id');
		$model = $this->getModel('Timeslot', 'JongmanModel', array('ignore_request'=>'true'));
		$data = new StdClass();
		$data->periods = $model->getTimeSlots($layout_id);
		
		$this->sendResponse($data);
	}
}