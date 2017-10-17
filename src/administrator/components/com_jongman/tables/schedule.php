<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
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
	function __construct($db)
	{
		parent::__construct('#__jongman_schedules', 'id', $db);
	}
	
	public function bind($src, $ignore = array())
	{
		if (isset($src['params']) && is_array($src['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($src['params']);
			$src['params'] = (string) $registry;
		}
		
		return parent::bind($src, $ignore);		
	}
	
	public function check()
	{
		// Check for valid name.
		if (trim($this->name) === '') {
			$this->setError(JText::_('COM_JONGMAN_ERROR_SCHEDULE_TITLE'));
			return false;
		}
		
		if (empty($this->layout_id)){
			$this->setError(JText::_('COM_JONGMAN_ERROR_SCHEDULE_LAYOUT'));
			return false;		
		}
		
		return true;
	}
		
    function _getAssetName() 
    {
		$k = $this->_tbl_key;
		return 'com_jongman.schedule.'.(int) $this->$k;
    }
}