<?php
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