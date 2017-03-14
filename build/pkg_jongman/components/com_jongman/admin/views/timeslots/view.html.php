<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Timeslots view.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanViewTimeslots extends JViewLegacy
{
	public function display($tp=null)
	{
		$this->is_j25     	= version_compare(JVERSION, '3', 'lt');
		parent::display($tp);
	}
}