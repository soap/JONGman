<?php
defined('_JEXEC') or die;

require_once(JPATH_COMPONENT.'/libraries/jongman.defines.php');
// JONgman class bootstrap 
jimport('jongman.framework');
JLoader::register('JFormFieldUser', JPATH_COMPONENT.'/models/fields/user.php');
JLoader::register('JongmanHelper', JPATH_COMPONENT . '/helpers/jongman.php');

// Get an instance of the controller prefixed by Jongman, assign default view 's name in case of no view or task
$controller = JControllerLegacy::getInstance('jongman');
// Perform the Request task, default task performed if none specified
$controller->execute(JRequest::getCmd('task'));
// Redirect if set by the controller
$controller->redirect();

