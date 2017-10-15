<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFBlackout
{
	/**
	 * @var RFDateRange
	 */
	protected $date;

	/**
	 * @var
	 */
	protected $id;

	/**
	 * @param RFDateRange $blackoutDate
	 */
	public function __construct($blackoutDate)
	{
		$this->date = $blackoutDate;
	}

	/**
	 * @return RFDateRange
	 */
	public function date()
	{
		return $this->date;
	}

	/**
	 * @return RFDate
	 */
	public function startDate()
	{
		return $this->date->getBegin();
	}

	/**
	 * @return RFDate
	 */
	public function endDate()
	{
		return $this->date->getEnd();
	}

	/**
	 * @param int $id
	 */
	public function withId($id)
	{
		$this->id = $id;
	}

	/**
	 * @return int
	 */
	public function id()
	{
		return $this->id;
	}
}
