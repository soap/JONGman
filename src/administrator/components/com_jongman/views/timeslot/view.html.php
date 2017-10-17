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
 * Timeslot view.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanViewTimeslot extends JViewLegacy
{
	public function display($tp=null)
	{
		if ($this->getLayout() !== 'success') {
			$doc = JFactory::getDocument();
			$doc->addScript(JURI::root().'media/com_jongman/jquery/js/jquery-1.8.2.min.js');
			$doc->addScript(JURI::root().'media/com_jongman/jquery/js/jquery.noconflict.js');
			$doc->addScript(JURI::root().'media/com_jongman/jongman/js/schedule-manager.js');
			$this->item = $this->get("Item");
			$this->form = $this->get("Form");
		}
		
		parent::display($tp);
	}
}