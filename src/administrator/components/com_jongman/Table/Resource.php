<?php
/**

* @package     JONGman Package

*

* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.

* @license     GNU General Public License version 2 or later; see LICENSE.txt

*/

namespace Soap\Component\Jongman\Administrator\Table;

// no direct access
defined('_JEXEC') or die;

use Joomla\CMS\Table\Table;

class Resource extends Table
{
	/**
	 * @param	JDatabaseDriver	A database connector object
	 */
	function __construct(\JDatabaseDriver $db){

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
	protected function _getAssetParentId(JTable $table = null, $id = null)
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