<?php
/**
 * @package      lib_jongman
 *
 * @author       Prasit Gebsaap (mrs.siam)
 * @copyright    Copyright (C) 2007-2013 Prasit Gebsaap. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.txt
 */

defined('_JEXEC') or die();


// Make sure the cms libraries are loaded
if (!defined('JPATH_PLATFORM')) {
    require_once dirname(__FILE__) . '/../cms.php';
}

if (!defined('JM_LIBRARY')) {
    define('JM_LIBRARY', 1);
}
else {
    // Make sure we run the code below only once
    return;
}

// Register the jongman library
JLoader::registerPrefix('JM', JPATH_PLATFORM . '/jongman');
JLoader::register('ScheduleLayout', JPATH_PLATFORM . '/jongman/domain/schedulelayout.php');
JLoader::register('SchedulePeriod', JPATH_PLATFORM . '/jongman/domain/scheduleperiod.php');

// Add include paths
JHtml::addIncludePath(JPATH_PLATFORM . '/jongman/html');
//JModelLegacy::addIncludePath(JPATH_PLATFORM . '/jongman/model', 'PFModel');
//JTable::addIncludePath(JPATH_PLATFORM . '/jongman/table', 'PFTable');
JForm::addFieldPath(JPATH_PLATFORM . '/jongman/form/fields');
JForm::addRulePath(JPATH_PLATFORM . '/jongman/form/rules');


// Define version
if (!defined('JMVERSION')) {
	// Do we really need this 
	jimport('jongman.version.version');
    $jmversion = new JMVersion();

    define('JMVERSION', $jmversion->getShortVersion());
}
