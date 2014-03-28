<?php
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
		$this->item = $this->get("Item");
		$this->form = $this->get("Form");
		
		parent::display($tp);
	}
}