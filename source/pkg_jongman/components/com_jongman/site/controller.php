<?php
/**
 * @version		$Id: controller.php 502 2012-12-24 13:15:38Z mrs.siam $
 * @copyright	Copyright (C) 2007 - 2013 Prasit Gebsaap. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * JONGman default controller.
 * this controller used for URL in form of option=com_jongman&view=vname or option=com_jongman
 *
 * @package		JONGman
 * @subpackage	JONGman Component
 * @since		2.0
 */
class JongmanController extends JControllerLegacy
{
	/**
	 * Method to display a view.
	 *
	 * @param	boolean				If true, the view output will be cached
	 * @param	array				An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JControllerLegacy	This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{	
		$jquery_site = JComponentHelper::getParams('com_jongman')->get('jquery_site');
		
		if ($jquery_site == 1) {
			JHtml::_('script', 'com_jongman/jquery/jquery-1.8.2.min.js', false, true);
			JHtml::_('script', 'com_jongman/jquery/jquery-ui-1.9.0.custom.min.js', false, true);		
			JHtml::_('script', 'com_jongman/jquery/jquery.qtip.min.js', false, true);
			JHtml::_('script', 'com_jongman/jquery/jquery.noconflict.js', false, true);	
			JHtml::_('stylesheet', 'com_jongman/jongman/jquery.qtip.min.css', false, true, false, false, false);							
		}
		$view = JFactory::getApplication()->input->getCmd('view');
		if ($view =='schedule') {
			if (JRequest::getCmd('layout')=='calendar') {
				$file = JUri::root().'media/com_jongman/jongman/css/smoothness/jquery-ui-1.9.0.custom.min.css';
				JHtml::_('stylesheet', $file, false, true, false, false, false);	
				JHtml::_('stylesheet', 'com_jongman/jongman/schedule.css', false, true, false, false, false);
				JHtml::_('stylesheet', 'com_jongman/jongman/calendar.css', false, true, false, false, false);
				JHtml::_('stylesheet', 'com_jongman/jongman/popup-reservation.css', false, true, false, false, false);
				JHtml::_('script', 'com_jongman/jongman/resource-popup.js', false, true);						
				JHtml::_('script', 'com_jongman/jongman/schedule.js', false, true);						
			}
		}
		if ($view == 'reservation') {
			JHtml::_('stylesheet', 'com_jongman/jongman/popup-reservation.css', false, true, false, false, false);
			JHtml::_('script', 'com_jongman/jongman/reservation.js', false, true);	
		}

		JHtml::_('stylesheet', 'com_jongman/jongman/jongman.css', false, true, false, false, false);
		parent::display($cachable, $urlparams);
		
		return $this;
	}
	
	public function getModel($name = '', $prefix = '', $config = array()) 
	{
		$view = JFactory::getApplication()->input->getCmd('view'); 
		if ($name == 'users' || (empty($name) && $view == 'users') ) {
			JModel::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_users/models/', 'UsersModel');
			$name = 'users';
			$prefix = 'UsersModel';		
		} 
		
		$id = JFactory::getApplication()->input->getInt('id', null);
		if ($name == 'reservation' && $id > 0) {
			$name = 'instance';	
		}
		
		return parent::getModel($name, $prefix, $config);
	}
}
