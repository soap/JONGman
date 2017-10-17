<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

abstract class RFFactory
{
	public static $authorisationService = null;
	public static $params = null;
	public static $scheduleLayouts = array();
	public static $reservationLayouts = array();	
	
	public static function getAuthorisationService()
	{
		if (!self::$authorisationService) {
			jimport('jongman.cms.authorisation.service');
			self::$authorisationService = new RFAuthorisationService(JFactory::getUser());
		}
	
		return self::$authorisationService;
	}
	
	public static function getParams()
	{
		if (!isset(self::$params)) {
			self::$params = RFParamsService::getInstance();
		}
			
		return self::$params;	
	}
	
	public static function getReservationLayout($targetTimezone)
	{
		$hash = md5($targetTimezone);
		if (!isset(self::$reservationLayouts[$hash])) {
			self::$reservationLayouts[$hash] = self::createReservationLayout($targetTimezone);
		}
		
		return self::$scheduleLayouts[$hash];
	}
	
	public static function getScheduleLayout($targetTimezone)
	{
		$hash = md5($targetTimezone);
		if (!isset(self::$scheduleLayouts[$hash])) {
			self::$scheduleLayouts[$hash] = self::createScheduleLayout($targetTimezone);
		}
	
		return self::$scheduleLayouts[$hash];
	}
		
	protected static function createReservationLayout($targetTimezone)
	{
		return new RFLayoutReservation($targetTimezone);
	}
	
	protected static function createScheduleLayout($targetTimezone)
	{
		return new RFLayoutSchedule($targetTimezone);	
	}
	
}