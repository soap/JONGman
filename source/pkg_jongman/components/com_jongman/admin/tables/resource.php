<?php
/**
 * @version		$Id$
 * @copyright	Copyright (C) 2011 Prasit Gebsaap, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * @package		Joomla.Administrator
 * @subpackage	Jongman
 */
class JongmanTableResource extends JTable
{
	/**
	 * @param	JDatabase	A database connector object
	 */
	function __construct(&$db){
		parent::__construct('#__jongman_resources', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}
		// Bind the rules. 
		if (isset($array['rules']) && is_array($array['rules'])) { 
			$rules = new JRules($array['rules']); 
			$this->setRules($rules); 
		}
		return parent::bind($array, $ignore);
	}
	
	/**
	* We provide our global ACL as parent
    * @see JTable::_getAssetParentId()
    */
	protected function _getAssetParentId($table = null, $id = null)
	{
		$asset = JTable::getInstance('Asset');
		$asset->loadByName('com_jongman');
		return $asset->id;
	}
	
    function _getAssetName() {
		$k = $this->_tbl_key;
		return 'com_jongman.resource.'.(int) $this->$k;
    }
}