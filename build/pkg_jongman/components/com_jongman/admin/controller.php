<?php
/**
 * @version		$Id: controller.php 487 2012-11-03 18:06:49Z mrs.siam $
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * JONGman default controller.
 * this controller used for URL in form of option=com_jongman&view=vname or option=com_jongman
 *
 * @package		Joomla.Administrator
 * @subpackage	com_jongman
 * @since		2.0
 */
class JongmanController extends JControllerLegacy
{
	protected $default_view = 'cpanel';
	/**
	 * Method to display a view.
	 *
	 * @param	boolean			If true, the view output will be cached
	 * @param	array			An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return	JController		This object to support chaining.
	 * @since	1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$view	= JRequest::getWord('view','cpanel');
		$layout = JRequest::getWord('layout', 'default');

        $view_list = array('cpanel', 'layouts', 'schedules', 'resources', 'quotas', 'reservations', 'blackouts', 'customers');
        if (in_array($view, $view_list) ){
            // Load the submenu.
            JongmanHelper::addSubmenu(JRequest::getWord('view', $this->default_view));    
        }
        
		JHtml::_('stylesheet', 'com_jongman/jongman/toolbar.css', false, true, false, false, false);		
		JHtml::_('stylesheet', 'com_jongman/administrator/styles.css', false, true, false, false, false);
        
		
		parent::display();

		return $this;
	}
}
