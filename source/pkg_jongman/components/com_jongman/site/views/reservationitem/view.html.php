<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Reservationitem view.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanViewReservationitem extends JViewLegacy
{
	public function display($tpl = null)
	{
		$this->item = $this->get("Item");
		
		parent::display($tpl);
	}
}