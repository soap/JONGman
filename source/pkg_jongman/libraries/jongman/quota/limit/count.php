<?php
defined('_JEXEC') or die;


class RFQuotaLimitCount implements IQuotaLimit
{
	/**
	 * @var array|int[]
	 */
	private $aggregateCounts = array();

	/**
	 * @var int
	*/
	private $totalAllowed;

	/**
	 * @param int $totalAllowed
	 */
	public function __construct($totalAllowed)
	{
		$this->totalAllowed = (int) $totalAllowed;
	}

	public function tryAdd($start, $end, $key)
	{
		if (array_key_exists($key, $this->aggregateCounts))
		{
			$this->aggregateCounts[$key] = $this->aggregateCounts[$key] + 1;
		}
		else
		{
			$this->aggregateCounts[$key] = 1;
		}
		
		if ($this->aggregateCounts[$key] > $this->totalAllowed)
		{
			throw new RFQuotaExceededException("Only {$this->totalAllowed} reservations are allowed for this duration");
		}
	}

	/**
	 * @return decimal
	 */
	public function amount()
	{
		return $this->totalAllowed;
	}

	/**
	 * @return string|QuotaUnit
	 */
	public function name()
	{
		return RFQuotaUnit::Reservations;
	}
}
