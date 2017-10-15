<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
jimport('joomla.application.controller.form');

class RFControllerForm extends JControllerForm
{
	protected $return_key;
	
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		// Set the view name
		if (empty($this->return_key))
		{
			if (array_key_exists('return_key', $config))
			{
				$this->return_key = $config['return_key'];
			}
			else
			{
				$this->return_key = 'com_jongman.'.$this->getName().'.return_page';
			}
		}
	}
	
	protected function setReturnPage($key=null)
	{
		if (empty($key)) $key = $this->return_key;
		
		$app = JFactory::getApplication();
		$return = $app->input->get('return', null, 'base64');
		if (empty($return)) {
			$referer = getenv("HTTP_REFERER");
			if (empty($referer)) return false;
	
			$return = base64_encode($referer);
		}
	
		$app->setUserState($key, $return);
		return true;
	}
	
	protected function clearReturnPage($key=null)
	{
		if (empty($key)) $key = $this->return_key;
		
		$app = JFactory::getApplication();
		$app->setUserState($key, null);
	}
	
	protected function getReturnPage($key=null, $clear = false)
	{
		if (empty($key)) $key = $this->return_key;
		
		$app = JFactory::getApplication();
		$return = $app->input->get('return', null, 'base64');
		if (empty($return)) {
			$return = $app->getUserState($key);
		}
	
		if ($clear) $this->clearReturnPage($key);
	
		return base64_decode($return);
	}
}