<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 *  View Class for Jongman component
 *
 */
class JongmanViewResourceitem extends JViewLegacy
{
    protected $item;
    protected $return_page;
    protected $state;
    protected $pageclass_sfx;
    protected $toolbar;


    public function display($tpl = null)
    {
    	// Initialise variables.
        $app    = JFactory::getApplication();
        $user   = JFactory::getUser();
		$layout = $this->getLayout();
		
        // Get model data.
        $this->state       = $this->get('State');
        $this->item        = $this->get('Item');
        
        if ($layout !== 'ajaxpopup') {
        	$this->toolbar     = $this->getToolbar();	
        }else{
        	$authorised = true;
        }


        if ($authorised !== true) {
            JError::raiseError(403, JText::_('JERROR_ALERTNOAUTHOR'));
            return false;
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            JError::raiseWarning(500, implode("\n", $errors));
            return false;
        }

        // Display the view
        parent::display($tpl);
        
    }

    /**
     * Generates the toolbar for the top of the view
     *
     * @return    string    Toolbar with buttons
     */
    protected function getToolbar()
    {
		return '';
    }
}