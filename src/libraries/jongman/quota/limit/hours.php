<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFQuotaLimitHours implements IQuotaLimit
{
	/**
	 * @var array|DateDiff[]
	 */
	private $aggregateCounts = array();

	/**
	 * @var \DateDiff
	*/
	private $allowedDuration;

	/**
	 * @var decimal
	 */
	private $allowedHours;

	/**
	 * @param decimal $allowedHours
	 */
	public function __construct($allowedHours)
	{
		$this->allowedHours = $allowedHours;
		$this->allowedDuration = new RFDateDiff($allowedHours * 3600);
	}

	/**
	 * @param Date $start
	 * @param Date $end
	 * @param string $key
	 * @return void
	 * @throws QuotaExceededException
	 */
	public function tryAdd($start, $end, $key)
	{
		$diff = $start->getDifference($end);

		if (array_key_exists($key, $this->aggregateCounts))
		{
			$this->aggregateCounts[$key] = $this->aggregateCounts[$key]->Add($diff);
		}
		else
		{
			$this->aggregateCounts[$key] = $diff;
		}

		if ($this->aggregateCounts[$key]->greaterThan($this->allowedDuration))
		{
			throw new RFQuotaExceededException("Cumulative reservation length cannot exceed {$this->allowedHours} hours for this duration");
		}
	}

	/**
	 * @return decimal
	 */
	public function amount()
	{
		return $this->allowedHours;
	}

	/**
	 * @return string|QuotaUnit
	 */
	public function name()
	{
		return RFQuotaUnit::Hours;
	}
}