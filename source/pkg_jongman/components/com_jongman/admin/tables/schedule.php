<?php
/**
 * @version		$Id: schedule.php 1 2011-01-20 02:40:25Z prasit.gebsaap $
 * @copyright	Copyright (C) 2011 Prasit Gebsaap, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	Jongman
 */
class JongmanTableSchedule extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db){
		parent::__construct('#__jongman_schedules', 'id', $db);
	}

    function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_jongman.schedule.'.(int) $this->$k;
    }
}