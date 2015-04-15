<?php
defined('_JEXEC') or die;

interface IScheduleRepository
{
	public function loadById($scheduleId);
	
	public function getAll();
	
	public function getLayout($scheduleId, ILayoutFactory $layoutFactory);
}