<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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

