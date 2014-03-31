<?php
// No direct access.
defined('_JEXEC') or die;

class JongmanHelper {
    
    /**
	 * Configure the Linkbar.
	 *
	 * @param	string	The name of the active view.
	 *
	 * @return	void
	 * @since	1.6
	 */
    public static function addSubmenu($vName)
    {
  		JSubMenuHelper::addEntry(
			JText::_('COM_JONGMAN_CPANEL'),
			'index.php?option=com_jongman&view=cpanel',
			$vName == 'cpanel'
		);
		  
        JSubMenuHelper::addEntry(
			JText::_('COM_JONGMAN_SUBMENU_LAYOUTS'),
			'index.php?option=com_jongman&view=layouts',
			$vName == 'layouts'
		);
		  
        JSubMenuHelper::addEntry(
			JText::_('COM_JONGMAN_SUBMENU_SCHEDULES'),
			'index.php?option=com_jongman&view=schedules',
			$vName == 'schedules'
		);
		
        JSubMenuHelper::addEntry(
			JText::_('COM_JONGMAN_SUBMENU_RESOURCES'),
			'index.php?option=com_jongman&view=resources',
			$vName == 'resources'
		);
        
        JSubMenuHelper::addEntry(
			JText::_('COM_JONGMAN_SUBMENU_RESERVATIONS'),
			'index.php?option=com_jongman&view=reservations',
			$vName == 'reservations'
		);
		
		JSubMenuHelper::addEntry(
			JText::_('COM_JONGMAN_SUBMENU_QUOTAS'),
			'index.php?option=com_jongman&view=quotas',
			$vName == 'quotas'
		);
		/*        
       
        JSubMenuHelper::addEntry(
			JText::_('COM_JONGMAN_USERS'),
			'index.php?option=com_jongman&view=users',
			$vName == 'users'
		);
        
        JSubMenuHelper::addEntry(
			JText::_('COM_JONGMAN_GROUPS'),
			'index.php?option=com_jongman&view=groups',
			$vName == 'groups'
		);*/                  
    }
    
   	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param	int		The schedule Id.
	 *
	 * @return	JObject
	 * @since	1.6
	 */
	public static function getActions($scheduleId = 0, $resourceId = 0)
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		if (empty($scheduleId)) {
			$assetName = 'com_jongman';
		} else if (!empty($scheduleId)) {
			$assetName = 'com_jongman.schedule.'.(int) $scheduleId;
		}else {
            $assetName = 'com_jongman.resource.'.(int)$resourceId;
        }

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
		}

		return $result;
	}
	
	public static function getScheduleOptions()
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$query->select('id AS value, name AS text');
		$query->from('#__jongman_schedules');
		
		$dbo->setQuery($query);
		return $dbo->loadObjectList();
	} 
	/**
	 * 
	 * Get resources ready for HTML options tag created
	 * @param int $scheduleId
	 * @todo add access level control for resource
	 */
	public static function getResourceOptions($scheduleId = null)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$query->select('id AS value, title AS text');
		$query->from('#__jongman_resources');
		if ($scheduleId) {
			$query->where('schedule_id = '.(int)$scheduleId);	
		}
		
		$dbo->setQuery($query);
		return $dbo->loadObjectList();
	} 
	
	public static function getReservationCategoryOptions()
	{
		return array(
				JHtml::_('select.option', '1', JText::_('COM_JONGMAN_RESERVATION_ONLY')), 
				JHtml::_('select.option', '2', JText::_('COM_JONGMAN_BLACKOUT_ONLY')),
				JHtml::_('select.option', '3', JText::_('JALL'))
			);
	}
	
    public static function quickIconButton( $link, $image, $text )
	{
		$lang		= JFactory::getLanguage();
		$path = 'media/com_jongman/images/';
        
        $button = '<div style="float:'.($lang->isRTL() ? 'right' : 'left').'">'
                    .'<div class="icon">'
                    .'  <a href="'.$link.'">'
                    .'    '.JHtml::image(JURI::root().'media/com_jongman/images/'.$image, $text )
                    .'    <span>'.$text.'</span>'
                    .'  </a>'
                    .'</div>'
                .'</div>';
        return $button;
    }  

    public function getVersion() 
    {
    	$manifest = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.'/jongman.xml' ); 
    	return $manifest->version;
    }

}