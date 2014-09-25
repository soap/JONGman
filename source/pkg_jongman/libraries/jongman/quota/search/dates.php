<?php
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

	public function __construct(Date $start, Date $end)
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
