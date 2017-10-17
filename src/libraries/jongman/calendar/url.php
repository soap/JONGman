<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFCalendarUrl
{
	private $url;

	private function __construct($year, $month, $day, $type)
	{
		// TODO: figure out how to get these values without coupling to the query string
		$app = JFactory::getApplication();
		$input  = $app->input;
		$resourceId = $input->getInt('rid');
		$scheduleId = $input->getInt('sid'); 
	
		$menu = $app->getMenu()->getActive();
		
		if ($menu === null) {
			$itemId = $input->getCmd('Itemid');
			if (!empty($itemId)) {
				$menu = $app->getMenu()->getItem((int)$itemId);
			}else{
				
			}
		}
		
		$format =  $menu->link.'&dd=%d&mm=%d&yy=%d&caltype=%s&rid=%s&sid=%s';
		$this->url = JRoute::_(sprintf($format, $day, $month, $year, $type, $resourceId, $scheduleId), false);
	}

	/**
	 * @static
	 * @param $date RFDate
	 * @param $type string
	 * @return RFCalendarUrl
	 */
	public static function create($date, $type)
	{
		return new RFCalendarUrl($date->year(), $date->month(), $date->day(), $type);
	}

	public function __toString()
	{
		return $this->url;
	}
}