<?php
// No direct access.
defined('_JEXEC') or die;

class JongmanHelper {
    
    /**
	 * Configure the Linkbar (J2.5) or Sidebar (J3.x).
	 *
	 * @param	string	The name of the active view.
	 * @return	void
	 * @since	1.0
	 */
    public static function addSubmenu($vName)
    {
    	$is_j3 = version_compare(JVERSION, '3.0.0', 'ge');
    	$class      = ($is_j3 ? 'JHtmlSidebar' : 'JSubMenuHelper');
    	
    	call_user_func(
    		array($class, 'addEntry'),
    		JText::_('COM_JONGMAN_CPANEL'),
			'index.php?option=com_jongman&view=cpanel',
			$vName == 'cpanel'
    	);

		call_user_func(
    		array($class, 'addEntry'),
			JText::_('COM_JONGMAN_SUBMENU_LAYOUTS'),
			'index.php?option=com_jongman&view=layouts',
			$vName == 'layouts'    		
    	);	
    	  
    	call_user_func(
    		array($class, 'addEntry'),
    		JText::_('COM_JONGMAN_SUBMENU_SCHEDULES'),
			'index.php?option=com_jongman&view=schedules',
			$vName == 'schedules'    		
    	);	

		call_user_func(
    		array($class, 'addEntry'),
			JText::_('COM_JONGMAN_SUBMENU_RESOURCES'),
			'index.php?option=com_jongman&view=resources',
			$vName == 'resources'    		
    	);		
		
		call_user_func(
			array($class, 'addEntry'),
			JText::_('COM_JONGMAN_SUBMENU_QUOTAS'),
			'index.php?option=com_jongman&view=quotas',
			$vName == 'quotas'
		);
		
		call_user_func(
    		array($class, 'addEntry'),
			JText::_('COM_JONGMAN_SUBMENU_RESERVATIONS'),
			'index.php?option=com_jongman&view=reservations',
			$vName == 'reservations'    		
    	);      

		call_user_func(
			array($class, 'addEntry'),
			JText::_('COM_JONGMAN_SUBMENU_BLACKOUTS'),
			'index.php?option=com_jongman&view=blackouts',
			$vName == 'blackouts'
		);
		             
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
	
	public static function getReservationOptions()
	{
		// Build the active state filter options.
		$options = array();

		$options[] = JHtml::_('select.option', '1', 'COM_JONGMAN_RESERVATION_STATUS_CREATED');
		$options[] = JHtml::_('select.option', '-1', 'COM_JONGMAN_RESERVATION_STATUS_PENDING');
		$options[] = JHtml::_('select.option', '*', 'JALL');
	
		return $options;
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

    public static function quickIcon($link, $image, $text)
    {
    	$html	= array();
    	$html[] =  "<a href=\"{$link}\" class=\"thumbnail btn pull-left\">";
    	$html[] =  JHtml::image(JURI::root().'media/com_jongman/images/'.$image, $text);
    	$html[] = "<span class=\"small\">{$text}</span>";
    	$html[] = "</a>";
    	
    	return implode('', $html);    	
    }
	/**
	 * 
	 * Get user timezone first from user parameter otherwise global configuration offset
	 * @param int $uid
	 */
	public static function getUserTimezone($uid = null) 
	{
		if ($uid === null) {
			$user = JFactory::getUser();
		}else{
			$user = JFactory::getUser($uid);
		}
		$timezone = $user->getParam('timezone', JFactory::getConfig()->get('offset', 'UTC'));
		
		return $timezone;
	}
	
	
	/**
	 * @param string $repeatType must be option in RepeatType enum
	 * @param int $interval
	 * @param RFDate $terminationDate
	 * @param array $weekdays
	 * @param string $monthlyType
	 * @return IRepeatOptions
	 */
	public static function getRepeatOptions($repeatType, $interval, $terminationDate, $weekdays, $monthlyType)
	{
		switch ($repeatType) {
			case 'daily':
				return new RFReservationRepeatDaily($interval, $terminationDate);
				break;
			case 'weekly' :
				return new RFReservationRepeatWeekly($interval, $terminationDate, $weekdays);
				break;
			case 'monthly' :
				$class = 'RFReservationRepeat'.ucfirst($input['repeat_monthly_type']);
				return new $class($interval, $terminationDate);
				break;
			case 'yearly' :
				return new RFReservationRepeatYearly($interval, $terminationDate);
				break;
		}
	
		return new RFReservationRepeatNone();
	}
	
	
    public static function getVersion() 
    {
    	$manifest = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.'/jongman.xml' ); 
    	return $manifest->version;
    }

}