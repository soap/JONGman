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
		require_once(JPATH_COMPONENT.'/helpers/jongman.php');
		
		if (JRequest::getCmd('view')=='schedule') {
			if (JRequest::getCmd('layout')=='calendar') {
					JHtml::_('stylesheet', 'com_jongman/jongman/calendar.css', false, true, false, false, false);		
			}
		}
		if (JRequest::getCmd('view')=='reservation') {
			JHtml::_('stylesheet', 'com_jongman/jongman/jongman.css', false, true, false, false, false);
			JHtml::_('stylesheet', 'com_jongman/jongman/popup-reservation.css', false, true, false, false, false);
			JHtml::_('script', 'com_jongman/jongman/reservation.js', false);	
		}

		parent::display($cachable, $urlparams);
		
		return $this;
	}
}
