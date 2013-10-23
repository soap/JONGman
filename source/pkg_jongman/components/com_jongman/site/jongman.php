<?php
// No direct access to this file
defined('_JEXEC') or die;
//ini_set('display_errors', 1);

require_once(JPATH_COMPONENT.'/libraries/jongman.defines.php');

// import joomla controller library
jimport('joomla.application.component.controller');
// Get an instance of the controller prefixed by Jongman, assign default view 's name in case of no view or task
$controller = JController::getInstance('Jongman', array('default_view'=>'calendar'));
// Perform the Request task, default task performed if none specified
$controller->execute(JRequest::getCmd('task'));
// Redirect if set by the controller
$controller->redirect();

