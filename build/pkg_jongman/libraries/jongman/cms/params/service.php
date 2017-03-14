<?php
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