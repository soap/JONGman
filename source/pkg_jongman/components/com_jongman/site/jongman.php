<?php
defined('_JEXEC') or die;

// JONgman class bootstrap 
jimport('jongman.framework');
JLoader::register('JFormFieldUser', JPATH_COMPONENT.'/models/fields/user.php');
JLoader::register('JongmanHelper', JPATH_COMPONENT . '/helpers/jongman.php');
JLoader::registerPrefix('J', JPATH_ADMINISTRATOR.'/components/com_jongman/helpers', false, true);

JLog::addLogger(
	array('text_file' => 'com_jongman.logs.php'),
	// Sets messages of all log levels to be sent to the file
    JLog::ALL,
    // Category for log messages
    array('error', 'warning', 'validation')
	);

// Get an instance of the controller prefixed by Jongman, assign default view 's name in case of no view or task
$controller = JControllerLegacy::getInstance('jongman');
// Perform the Request task, default task performed if none specified
$controller->execute(JFactory::getApplication()->input->getCmd('task'));
// Redirect if set by the controller
$controller->redirect();

