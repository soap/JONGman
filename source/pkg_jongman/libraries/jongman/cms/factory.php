<?php
defined('_JEXEC') or die;

abstract class RFFactory 
{
	public static $permissionService = null;
	public static $paramsService = null;
	public static $authorisationService = null;
	
	public static function getPermissionService()
	{
		if (!self::$permissionService) {
			// self::$permissionService = RFPermissionService::getInstance();	
		}
		
		return self::$permissionService;
	}
	
	public static function getParams()
	{
		if (!self::$params) {
			self::$paramsService = RFParamsService::getInstance();
		}
		
		return self::$paramsService;
	}
	
	public static function getAuthorisationService()
	{
		if (!self::$authorisationService) {
			jimport('jongman.cms.authorisation.service');
			self::$authorisationService = RFAuthorisationService::getInstance();
		}
		
		return self::$authorisationService;
	}
}