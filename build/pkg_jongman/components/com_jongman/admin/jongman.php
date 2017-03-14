<?php
/*------------------------------------------------------------------------
 JONGman - Visualized Reservation System extension for Joomla
 ------------------------------------------------------------------------
 @Author    Prasit Gebsaap
 @Website   http://www.joomlant.com
 @Copyright Copyright (C) 2013 - 2015 Prasit Gebsaap. All Rights Reserved.
 @License   GNU General Public License version 3, or later
 ------------------------------------------------------------------------*/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jongman')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}


// import joomla controller library
jimport('joomla.application.component.controller');
// Include dependancies
jimport('joomla.application.component.helper');
jimport('jongman.framework');
JLoader::register('JongmanHelper', JPATH_COMPONENT.'/helpers/jongman.php');
JLoader::registerPrefix('J', JPATH_COMPONENT.'/helpers', false, true);

// Get an instance of the controller prefixed by Jongman, assign default view 's name in case of no view or task
$controller = JControllerLegacy::getInstance('Jongman', array('default_view'=>'cpanel'));
// Perform the Request task, default task performed if none specified
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
// Redirect if set by the controller
$controller->redirect();

