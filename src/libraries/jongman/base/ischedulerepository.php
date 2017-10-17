<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IScheduleRepository
{
	public function loadById($scheduleId);
	
	public function getAll();
	
	public function getLayout($scheduleId, ILayoutFactory $layoutFactory);
}