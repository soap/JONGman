<?php
defined('_JEXEC') or die;


class RFDNullDateRange extends RFDateRange
{
	protected static $instance;

	public function __construct()
	{
		parent::__construct(RFDate::Now(), RFDate::Now());
	}

	/**
	 * @return NullDateRange
	 */
	public static function getInstance()
	{
		if(self::$instance == null)
		{
			self::$instance = new NullDateRange();
		}

		return self::$instance;
	}
}

