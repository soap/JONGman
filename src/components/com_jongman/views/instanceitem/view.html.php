<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Reservationitem view.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanViewInstanceitem extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->item = $this->get("Item");
		$this->resources = $this->get("Resources");
		
		parent::display($tpl);
	}
	
	public function getDisplayResourceName($resources)
	{
		$names = array();
		foreach($resources as $resource) {
			$names[] = $resource->title;	
		}
		
		return implode(',', $names);
	}
}