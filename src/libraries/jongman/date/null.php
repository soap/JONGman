<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFDateNull extends RFDate
{
	/**
	 * @var NullDate
	 */
	private static $ndate;

	public function __construct()
	{
		//parent::__construct();
	}

	public static function getInstance()
	{
		if (self::$ndate == null)
		{
			self::$ndate = new RFDateNull();
		}

		return self::$ndate;
	}

	public function format($format)
	{
		return '';
	}

	public function toString()
	{
		return '';
	}

	public function toDatabase()
	{
		return null;
	}

	public function toTimezone($timezone)
	{
		return $this;
	}
}
