<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFArrayDiff
{
	private $_added = array();
	private $_removed = array();
	private $_unchanged = array();

	public function __construct($array1, $array2)
	{
		$added = array_diff($array2, $array1);
		$removed = array_diff($array1, $array2);
		$unchanged = array_intersect($array1, $array2);

		if (!empty($added))
		{
			$this->_added = array_merge($added);
		}
		if (!empty($removed))
		{
			$this->_removed = array_merge($removed);
		}
		if (!empty($unchanged))
		{
			$this->_unchanged = array_merge($unchanged);
		}
	}

	public function areDifferent()
	{
		return !empty($this->_added) || !empty($this->_removed);
	}

	public function getAddedToArray1()
	{
		return $this->_added;
	}

	public function getRemovedFromArray1()
	{
		return $this->_removed;
	}

	public function getUnchangedInArray1()
	{
		return $this->_unchanged;
	}
}