<?php
defined('_JEXEC') or die;

interface IScheduleRepository
{
	public function loadById($scheduleId);
	
}