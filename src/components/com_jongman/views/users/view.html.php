<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class JongmanViewUsers extends JViewLegacy
{
	public function display($tp = null)
	{
		$this->items 		= $this->get("Items");
		$this->state 		= $this->get("State");
		$this->pagination 	= $this->get("Pagination");
		
		parent::display($tp);
	}
}