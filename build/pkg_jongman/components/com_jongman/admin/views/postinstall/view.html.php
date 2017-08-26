<?php
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * Postinstall view.
 *
 * @package     Cash Payment System
 * @subpackage  com_cashpayment
 * @since       2.0
 */
class JongmanViewPostinstall extends JViewLegacy
{
	public function display($tpl = null) 
	{
		JHtml::_('stylesheet', 'com_jongman/jongman/postinstall.css', false, true, false, false, false);
		JHtml::_('stylesheet', 'com_jongman/bootstrap/bootstrap.css', false, true, false, false, false);
		
		$this->addToolbar();
		parent::display($tpl);
	}
	
	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_JONGMAN_POSTINSTALL_TITLE'));
		JToolbarHelper::cancel('postinstall.cancel');
	}
}