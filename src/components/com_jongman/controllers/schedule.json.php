<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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