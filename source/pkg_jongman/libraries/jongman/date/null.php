<?php
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
			self::$ndate = new NullDate();
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
