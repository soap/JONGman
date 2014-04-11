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

//base interface definitions
jimport('jongman.base.ilayout');
jimport('jongman.base.irepeats');
jimport('jongman.base.ireservationlisting');
jimport('jongman.base.ireservationslot');
jimport('jongman.base.ireservationvalidationrulle');
jimport('jongman.base.ireserveditem');
jimport('jongman.base.iseriesupdatescope');
jimport('jongman.base.seriesevent');
jimport('jongman.base.ireservationvalidationresult');

// I want to user JM prefix, but JLoader cannot support it, as Joomla 's J matched first
JLoader::registerPrefix('RF', JPATH_PLATFORM . '/jongman');

//discover classes prefixed by RF in the folder
JLoader::discover('RF', JPATH_PLATFORM . '/jongman/utils');
JLoader::discover('RF', JPATH_PLATFORM . '/jongman/cms');
JLoader::register('RFReservationStartTimeContraint', JPATH_PLATFORM.'/jongman/utils/starttimeconstraint.php');
JLoader::register('RFReservationExistingSeries', JPATH_PLATFORM.'/jongman/reservation/existingseries.php');

// Add include paths
JHtml::addIncludePath(JPATH_PLATFORM . '/jongman/html');
JForm::addFieldPath(JPATH_PLATFORM . '/jongman/form/fields');
JForm::addRulePath(JPATH_PLATFORM . '/jongman/form/rules');


// Define version
if (!defined('RFVERSION')) {
	// Do we really need this 
	jimport('jongman.version.version');
    $jmversion = new JMVersion();

    define('RFVERSION', $jmversion->getShortVersion());
}
