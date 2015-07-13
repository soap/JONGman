<?php
defined('_JEXEC') or die;


class RFBlackoutItem implements IReservedItem
{
	/**
	 * @var Date
	 */
	public $startDate;

	/**
	 * @var Date
	 */
	public $endDate;

	/**
	 * @var DateRange
	 */
	public $date;

	/**
	 * @var int
	 */
	public $resourceId;

	/**
	 * @var string
	 */
	public $resourceName;

	/**
	 * @var int
	 */
	public $instanceId;

	/**
	 * @var int
	 */
	public $seriesId;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var int
	 */
	public $scheduleId;

	/**
	 * @var null|string
	 */
	public $firstName;

	/**
	 * @var null|string
	 */
	public $lastName;

	/**
	 * @var null|int
	 */
	public $ownerId;

	/**
	 * @param int $instanceId
	 * @param Date $startDate
	 * @param Date $endDate
	 * @param int $resourceId
	 * @param int $ownerId
	 * @param int $scheduleId
	 * @param string $title
	 * @param string $description
	 * @param string $firstName
	 * @param string $lastName
	 * @param string $resourceName
	 * @param int $seriesId
	 */
	public function __construct(
		$instanceId,
		RFDate $startDate,
		RFDate $endDate,
		$resourceId,
		$ownerId,
		$scheduleId,
		$title,
		$description,
		$firstName,
		$lastName,
		$resourceName,
		$seriesId)
	{
		$this->instanceId = $instanceId;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->resourceId = $resourceId;
		$this->ownerId = $ownerId;
		$this->scheduleId = $scheduleId;
		$this->title = $title;
		$this->description = $description;
		$this->firstName = $firstName;
		$this->lastName = $lastName;
		$this->resourceName = $resourceName;
		$this->seriesId = $seriesId;
		
		$this->date = new RFDateRange($startDate, $endDate);
	}

	/**
	 * @static
	 * @param $row
	 * @return RFBlackoutItem
	 */
	public static function populate($row)
	{
		return new RFBlackoutItem($row->instance_id,
									RFDate::fromDatabase($row->start_date),
									RFDate::fromDatabase($row->end_date),
									$row->resource_id,
									$row->owner_id,
									$row->schedule_id,
									$row->title,
									$row->description,
									$row->author_name,
									'',
									$row->resource_name,
									$row->blackout_id);
	}

	/**
	 * @return RFDate
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @return RFDate
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @return int
	 */
	public function getResourceId()
	{
		return $this->resourceId;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->instanceId;
	}

	/**
	 * @param RFDate $date
	 * @return bool
	 */
	public function occursOn(RFDate $date)
	{
		return $this->date->occursOn($date);
	}
}
