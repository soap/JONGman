<?php
//Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

define( 'BLACKOUT_ONLY', 1 );			// Define constants
define( 'RESERVATION_ONLY', 2 );
define( 'ALL', 3 );
define( 'READ_ONLY', 4 );

define('RES_TYPE_ADD', 'r');
define('RES_TYPE_MODIFY', 'm');
define('RES_TYPE_DELETE', 'd');
define('RES_TYPE_VIEW', 'v');
define('RES_TYPE_APPROVE', 'a');

define('SECONDS_IN_DAY', 86400);
//Shall we change the reservation approval to approved, rejected and pending? Currently we use is_pending = 1 or 0 as approval
define('RES_STATUS_APPROVED', 0);
define('RES_STATUS_PENDING', 1);
define('RES_STATUS_REJECTED', 2);