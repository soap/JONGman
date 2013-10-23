<?php
// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class JongmanViewClosepopup extends JView
{
	/**
	 * Display the view
	 */
	protected $success;
	protected $cancel;
	
	function display($tpl = null)
	{
		JRequest::setVar('tmpl','component');
		$this->cancel = false;
		if (JRequest::getInt('cancel') == 1) {
			$this->cancel = true;	
		}
		if (JRequest::getInt('refresh') == 1) { 
			$this->success = true; 
			$this->message = '';
		}else{
			$this->message = '';
			$this->success = false;
		}
		
		parent::display($tpl);

	}
}