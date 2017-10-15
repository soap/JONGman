<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFRepeatConfiguration
{
	/**
	 * @var string
	 */
	public $type;

	/**
	 * @var string
	 */
	public $interval;

	/**
	 * @var Date
	 */
	public $terminationDate;

	/**
	 * @var array
	 */
	public $weekdays;

	/**
	 * @var string
	 */
	public $monthlyType;

	/**
	 * @param string $repeatType
	 * @param string $configurationString
	 * @return RepeatConfiguration
	 */
	public static function create($repeatType, $configurationString)
	{
		$reg = new JRegistry($configurationString);
		$config = new RFRepeatConfiguration();
		$config->type = empty($repeatType) ? RFRepeatType::None : $repeatType;

		$config->interval = $reg->get('interval');
		$config->setTerminationDate($reg->get('termination'));
		$config->setWeekdays($reg->get('days'));
		$config->monthlyType = $reg->get('type');

		return $config;
	}

	protected function __construct()
	{
	}

	private function setTerminationDate($terminationDateString)
	{
		if (!empty($terminationDateString))
		{
			$this->TerminationDate = Date::FromDatabase($terminationDateString);
		}
	}

	private function setWeekdays($weekdays)
	{
		if (!empty($weekdays))
		{
			$this->Weekdays = explode(',', $weekdays);
		}
	}
}