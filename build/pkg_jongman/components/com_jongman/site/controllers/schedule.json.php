<?php
defined('_JEXEC') or die;

class JongmanControllerSchedule extends JControllerAdmin
{
	public function layout()
	{
		$data = array();
		echo json_encode($data);
		jexit();
	}
}