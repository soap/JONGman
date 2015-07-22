<?php
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

		$url = JRoute::_($app->getMenu()->getActive()->link, false);
		$format =  $url.'&dd=%d&mm=%d&yy=%d&caltype=%s&rid=%s&sid=%s';
		$this->url = sprintf($format, $day, $month, $year, $type, $resourceId, $scheduleId);
		//var_dump($this->url);
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