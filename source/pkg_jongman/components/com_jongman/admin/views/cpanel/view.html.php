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
		JHtml::stylesheet( 'administrator/components/com_jongman/assets/css/jongman.css' );
		JHtml::_('behavior.tooltip');
		
        $this->is_j25     = version_compare(JVERSION, '3', 'lt');
		$this->version = JongmanHelper::getVersion();
        
	   	if ($this->getLayout() !== 'modal') {
            $this->addToolbar();

            // Add the sidebar (Joomla 3 and up)
            if (!$this->is_j25) {
                $this->addSidebar();
                $this->sidebar = JHtmlSidebar::render();
            }
        }
		parent::display($tpl);
	}
	
	protected function addToolbar() 
	{
		$canDo	= JongmanHelper::getActions();
		JToolBarHelper::title( '&nbsp;', 'jongman.png' );
		
		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_jongman');
			JToolBarHelper::divider();
		}
		
		JToolBarHelper::help( 'screen.jongman', true );
	}
	
    protected function addSidebar()
    {
        JHtmlSidebar::setAction('index.php?option=com_pfprojects&view=projects');
    }	
}