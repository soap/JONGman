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
		$item = new JObject();
		$item->id = JRequest::get('layout_id', 'int', '');
		
		$this->item = $item;
		parent::display($tp);
	}
}