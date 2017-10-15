<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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