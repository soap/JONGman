<?php
/**
 * @version: $Id$
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class JongmanHelper 
{
	
	/**
	 *
	 * Get allowed actions for current user
	 * @param string $assetName
	 * @return JObject each action is property with boolean atrribute
	 */
	public static function getActions($assetName = 'com_jongman')
	{
		$user	= JFactory::getUser();
		$result	= new JObject;
	
		$actions = array(
				'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete',
				'com_jongman.reservation.create', 'com_jongman.reservation.edit', 'com_jongman.reservation.edit.state',
				'com_jongman.reservation.edit.own', 'com_jongman.reservation.delete'
		);
	
		foreach ($actions as $action) {
			$result->set($action,	$user->authorise($action, $assetName));
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
     * return rule process with common rules for all actions
     * @return RFReservationValidationRuleprocessor 
     */
    public static function getRuleProcessor()
    {
    	$user = JFactory::getUser();
    	$rules = array();
    	$rules[] = new RFValidationRuleReservationDatetime();
    	$rules[] = new RFValidationRuleAdminexcluded(new RFValidationRuleReservationStarttime(), $user);
    	//$rules[] = new RFValidationRuleAdminExcluded(new RFValidationRulePermissionValidationRule(new PermissionServiceFactory()), $user);
    	//$rules[] = new RFValidationRuleAdminExcluded(new RFValidationRuleResourceMinimumNoticeRule(), $user);
    	//$rules[] = new RFValidationRuleAdminExcluded(new RFValidationRuleResourceMaximumNoticeRule(), $user);
    	//$rules[] = new RFValidationRuleAdminExcluded(new RFValidationRuleResourceParticipationRule(), $user);
    	//$rules[] = new CustomAttributeValidationRule(new RFValidationRuleAttributeRepository());
    	//$rules[] = new ReservationAttachmentRule();
    	return new RFReservationValidationRuleprocessor($rules);
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
    
    function getVersion()
    {
    	$version = stdClass();
    	$manifest = JFactory::getXML(JPATH_COMPONENT_ADMINISTRATOR.'/jongman.xml' ); 
    	$version->longText = 'JONGman '.$manifest->version;
        $version->shortText = $manifest->version;
        return $version;
    }
}