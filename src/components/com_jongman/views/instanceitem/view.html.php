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