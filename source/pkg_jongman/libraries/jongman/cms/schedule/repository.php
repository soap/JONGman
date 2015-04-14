<?php
defined('_JEXEC') or die;
jimport('jongman.base.ischedulerepository');

class RFScheduleRepository implements IScheduleRepository
{
	public function loadById($scheduleId)
	{
		$config = array('ignore_request'=>true);
		$model = JModelLegacy::getInstance('Schedule', 'JongmanModel', $config);
		$model->setState('schedule.id', $scheduleId);
		$item = $model->getItem();

		return $item;
	}
}