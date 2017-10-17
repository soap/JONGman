<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFQuotaSearchDates
{
	/**
	 * @var \Date
	 */
	private $start;

	/**
	 * @var \Date
	 */
	private $end;

	public function __construct(RFDate $start, RFDate $end)
	{
		$this->start = $start;
		$this->end = $end;
	}

	/**
	 * @return Date
	 */
	public function start()
	{
		return $this->start;
	}

	/**
	 * @return Date
	 */
	public function end()
	{
		return $this->end;
	}
}
