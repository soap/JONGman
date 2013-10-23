<?php
/**
 * @version		$Id: schedules.php 403 2011-10-02 00:14:24Z mrs.siam $
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * schedule list controller class.
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jongman
 * @since		1.6
 */
class JongmanControllerSchedules extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since	2.0
	 */
	public function &getModel($name = 'Schedule', $prefix = 'JongmanModel')
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

}