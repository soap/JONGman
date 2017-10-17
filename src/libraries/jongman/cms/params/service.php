<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFParamsService implements IParamsService
{
	protected static $_instance = null; 
	
	protected function __construct(){}
	
	public function getInstance()
	{
		if (!self::$_instance) {
			self::$_instance = new RFParamsService();
		}

		return self::$_instance;
	}
	
	public function get($name, $default) 
	{
		return JComponentHelper::getParams('com_jongman')->get($name, $default);
	}
}