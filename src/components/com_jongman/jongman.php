<?php
/*------------------------------------------------------------------------
 JONGman - Visualized Reservation System extension for Joomla
 ------------------------------------------------------------------------
 @Author    Prasit Gebsaap
 @Website   http://www.joomlant.com
 @Copyright Copyright (C) 2013 - 2015 Prasit Gebsaap. All Rights Reserved.
 @License   GNU General Public License version 3, or later
 ------------------------------------------------------------------------*/
defined('_JEXEC') or die;

// JONgman class bootstrap 
jimport('jongman.framework');
JLoader::register('JFormFieldUser', JPATH_COMPONENT.'/models/fields/user.php');
JLoader::register('JongmanHelper', JPATH_COMPONENT . '/helpers/jongman.php');
JLoader::register('JongmanHelperRoute', JPATH_COMPONENT . '/helpers/route.php');
JLoader::registerPrefix('J', JPATH_ADMINISTRATOR.'/components/com_jongman/helpers', false, true);
JLoader::registerPrefix('J', JPATH_ROOT.'/components/com_jongman/libraries', false, true);

$logEnabled = (bool)JComponentHelper::getParams('com_jongman')->get('logging_level', false);
if ( $logEnabled ) {
	
	$logLevel = JComponentHelper::getParams('com_jongman')->get('logging_level', JLog::WARNING);
	
	JLog::addLogger(
		array(
			'logger'=> 'formattedtext',
			'text_file' => 'com_jongman.logs.php'
		),
		// Sets messages of all log levels to be sent to the file
		$logLevel,
		// Category for log messages
		array('reservation', 'validation', 'other')
	);
}

// Get an instance of the controller prefixed by Jongman, assign default view 's name in case of no view or task
$controller = JControllerLegacy::getInstance('jongman');
// Perform the Request task, default task performed if none specified
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
// Redirect if set by the controller
$controller->redirect();

