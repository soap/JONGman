<?php
/**
 * @version: $Id$
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class JongmanHelper 
{
	
	/**
	 * Get allowed actions for current user
	 * @param string $assetName
	 * @return JObject each action is property with boolean attributes
	 */
	public static function getActions($assetName = 'com_jongman')
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
	
		$actions = array(
				'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete', 
				'core.edit.own', 'com_jongman.delete.own'
		);
	
		foreach ($actions as $action) {
			$result->set($action, $user->authorise($action, $assetName));
		}
	
		return $result;
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
	
	function isAdmin( $uid = null, $resource_id = null )
	{
		if (empty($uid)) {
			$user = JFactory::getUser();
		}else{
			$user = JFactory::getUser($uid);
		}
		
		if (!empty($resource_id)) {
			$assetName = 'com_jongman.resource.'.$resource_id;
		}else{
			$assetName = 'com_jongman';
		}
		return $user->authorise('core.admin', $assetName);
	}

    function getUserSelectList($tagname, $attribs = '', $selected)
    {
        $dbo = & JFactory::getDbo();
        $sql = "SELECT id as value, name as text FROM #__users WHERE block = 0";
        $dbo->setQuery($sql);
        $rows = $dbo->loadObjectList();
        
  		$html = JHTML::_('select.genericlist', $rows, $tagname, $attribs, 'value', 'text', $selected);
		
		return $html;   
    }
    
    /**
	 * @param string $repeatType must be option in RepeatType enum
	 * @param int $interval
	 * @param RFDate $terminationDate
	 * @param array $weekdays
	 * @param string $monthlyType
	 * @return IRepeatOptions
	 */
	public function getRepeatOptions($repeatType, $interval, $terminationDate, $weekdays, $monthlyType)
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
    
    /**
     * return rule process with common rules for all actions
     * @return RFReservationValidationRuleprocessor 
     */
    public static function getRuleProcessor()
    {
    	$user = JFactory::getUser();
    	$rules = array();
    	$rules[] = new RFReservationRuleReservationDatetime();
    	$rules[] = new RFReservationRuleAdminexcluded(new RFReservationRuleReservationStarttime(), $user);
    	//$rules[] = new AdminExcluded(new RFReservationRulePermissionValidationRule(new PermissionServiceFactory()), $user);
    	$rules[] = new RFReservationRuleAdminExcluded(new RFReservationRuleResourceMinimumNotice(), $user);
    	$rules[] = new RFReservationRuleAdminExcluded(new RFReservationRuleResourceMaximumNotice(), $user);
    	//$rules[] = new RFReservationRuleAdminExcluded(new RFReservationRuleResourceParticipationRule(), $user);
    	//$rules[] = new CustomAttributeValidationRule(new RFReservationRuleAttributeRepository());
    	//$rules[] = new ReservationAttachmentRule();
    	return new RFReservationValidationRuleProcessor($rules);
    }
    /**
     * 
     * Check if user can approve the reservation for specified resource
     * @param JUser $user
     * @param RFResourceBookable $resource
     * @return boolean
     */
    public function canApproveForResource(JUser $user, RFResourceBookable $resource ) 
    {
    	if ($user->authorise('core.admin','com_jongman')) {
    		return true;
    	}
    	
    	if ($user->authorise('core.edit', 'com_jongman.resource.'.$resource->getResourceId())) {
    		return true;
    	}
    	
    	if ($user->authorise('core.edit.state', 'com_jongman.resource.'.$resource->getResourceId())) {
    		return true;
    	}
    	
    	return false;
    }

    /**
     * @return string
     */
    function getVersion()
    {
    	$version = stdClass();
    	$manifest = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.'/jongman.xml' ); 
    	$version->longText = 'JONGman '.$manifest->version;
        $version->shortText = $manifest->version;
        return $version;
    }
}