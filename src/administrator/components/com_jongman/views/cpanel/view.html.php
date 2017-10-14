<?php
/*
 * @package		JONGman
 * @copyright Copyright (C) Prasit Gebsaap www.joomlant.org
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();
jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pane' );

class JongmanViewCpanel extends JViewLegacy
{
	function display($tpl = null) 
	{
		JHtml::_('behavior.tooltip');
		
		$this->version = JongmanHelper::getVersion();
		$libVersion 	= new JMVersion();
		$this->libVersion = $libVersion->getShortVersion();
		
	   	if ($this->getLayout() !== 'modal') {
            $this->addToolbar();
            $this->sidebar = JHtmlSidebar::render();
        }
		parent::display($tpl);
	}
	
	protected function addToolbar() 
	{
		$canDo	= JongmanHelper::getActions();
		JToolBarHelper::title( 'JONGman', 'jongman.png' );
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_jongman');
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::help( 'screen.jongman', true );
	}
}