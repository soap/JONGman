<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
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
		$view = $this->input->getCmd('view', null);
		if ($view === null) {
			$this->setMessage(JText::_('COM_JONGMAN_ERROR_DIRECT_ACCESS_NOT_ALLOW'), 'warning');	
			$this->setRedirect('index.php');
			return;
		}
		
		$this->_init();
		$params = JComponentHelper::getParams('com_jongman');

		$jongman_css = $params->get('jongman_css', '1');
		if ($jongman_css) {
			JHtml::_('stylesheet', 'com_jongman/jongman/jongman.css', false, true, false, false, false);
		}
		
		$layout = JFactory::getApplication()->input->getCmd('layout', null);
	
		if (empty($layout) && $view == 'schedule') {
			$layout = 'calendar';
			JFactory::getApplication()->input->set('layout', 'calendar');
		}
		if ($view =='schedule') {
			if ($layout=='calendar') {
				$file = JUri::root().'media/com_jongman/jongman/css/smoothness/jquery-ui-1.9.0.custom.min.css';
				JHtml::_('stylesheet', $file, false, true, false, false, false);	
				JHtml::_('stylesheet', 'com_jongman/jongman/schedule.css', false, true, false, false, false);
				JHtml::_('stylesheet', 'com_jongman/jongman/calendar.css', false, true, false, false, false);
				JHtml::_('stylesheet', 'com_jongman/jongman/popup-reservation.css', false, true, false, false, false);
				JHtml::_('script', 'com_jongman/jquery/jquery.ui.selectable.1.11.1.js', false, true);
				JHtml::_('script', 'com_jongman/jongman/resource-popup.js', false, true);						
				JHtml::_('script', 'com_jongman/jongman/schedule.js', false, true);						
			}
		}
		
		if (in_array($view, array('reservations', 'calendar'))) {
			JHtml::_('script', 'com_jongman/jquery/jquery.qtip.js', false, true);
			JHtml::_('stylesheet', 'com_jongman/jquery/jquery.qtip.css', false, true, false, false, false);
		}
		
		if ($view == 'reservation') {
			JHtml::_('stylesheet', 'com_jongman/jongman/front-reservation.css', false, true, false, false, false);
			JHtml::_('script', 'com_jongman/jongman/reservation.js', false, true);
			JHtml::_('script', 'com_jongman/jongman/date-helper.js', false, true);
			JHtml::_('script', 'com_jongman/jongman/recurrence.js', false, true);
		}
        parent::display($cachable, $urlparams);

		return $this;
	}
	
	public function getModel($name = '', $prefix = '', $config = array()) 
	{
		$view = JFactory::getApplication()->input->getCmd('view'); 
		if ($name == 'users' || (empty($name) && $view == 'users') ) {
			JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_users/models/', 'UsersModel');
			$name = 'users';
			$prefix = 'UsersModel';		
		} 

		// view=reservation id=0 new reservation use reservation model
        // otherwise use instance model for existing reservation edit
		$ref = JFactory::getApplication()->input->getCmd('reference_number', null);
		if ($name == 'reservation' && !empty($ref)) {
			$name = 'instance';	
		}

		return parent::getModel($name, $prefix, $config);
	}
	
	private function _init()
	{
		 $is_j25 = version_compare(JVERSION, '3', 'lt');
		 if (!$is_j25) {
		 	JHtml::_('bootstrap.framework');
		 	JHtml::_('jquery.framework');
		 	JHtml::_('jquery.ui',array('core', 'sortable'));								
		 	//JHtml::_('stylesheet', 'com_jongman/jongman/jquery.datetimepicker.css', false, true, false, false, false);
		 	JHtml::_('script', 'com_jongman/jquery/jquery.datepicker.js', false, true);		
		 }else{
		 	$params = JComponentHelper::getParams('com_jongman');
			$jquery_site = $params->get('jquery_site', '1');
			$bootstrap_js = $params->get('bootstrap_js', '1');
			$bootstrap_css = $params->get('bootstrap_css', '1');
			$jongman_css = $params->get('jongman_css', '1');
			if ($jquery_site == '1') {
				JHtml::_('script', 'com_jongman/jquery/jquery-1.8.2.min.js', false, true);
				JHtml::_('script', 'com_jongman/jquery/jquery-ui-1.9.0.custom.min.js', false, true);	
				JHtml::_('script', 'com_jongman/jquery/jquery.ui.selectable.js', false, true);
				JHtml::_('script', 'com_jongman/jquery/jquery.qtip.min.js', false, true);
				JHtml::_('script', 'com_jongman/jquery/jquery.noconflict.js', false, true);							
			}
			if ($bootstrap_js) {
				JHtml::_('script', 'com_jongman/bootstrap/bootstrap.min.js', false, true);	
			}
			if ($bootstrap_css) {
				JHtml::_('stylesheet', 'com_jongman/bootstrap/component.css', false, true, false, false, false);							
			}		 	
		 }	
	}
}
