<?php
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
