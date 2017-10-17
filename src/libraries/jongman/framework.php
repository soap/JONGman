<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;


// Make sure the cms libraries are loaded
if (!defined('JPATH_PLATFORM')) {
    require_once dirname(__FILE__) . '/../cms.php';
}

if (!defined('RF_FRAMEWORK')) {
    define('RF_FRAMEWORK', 1);
}
else {
    // Make sure we run the code below only once
    return;
}

jimport('joomla.filesystem.folder');


// Include the library
require_once dirname(__FILE__) . '/library.php';

