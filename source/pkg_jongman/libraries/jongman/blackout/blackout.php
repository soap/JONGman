<?php
defined('_JEXEC') or die;

class RFBlackout
{
	/**
	 * @var DateRange
	 */
	protected $date;

	/**
	 * @var
	 */
	protected $id;

	/**
	 * @param DateRange $blackoutDate
	 */
	public function __construct($blackoutDate)
	{
		$this->date = $blackoutDate;
	}

	/**
	 * @return DateRange
	 */
	public function date()
	{
		return $this->date;
	}

	/**
	 * @return Date
	 */
	public function startDate()
	{
		return $this->date->getBegin();
	}

	/**
	 * @return Date
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
