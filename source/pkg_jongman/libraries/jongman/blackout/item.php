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
		JMDate $startDate,
		JMDate $endDate,
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
		$this->date = new DateRange($startDate, $endDate);
	}

	/**
	 * @static
	 * @param $row
	 * @return BlackoutItemView
	 */
	public static function populate($row)
	{
		return new BlackoutItem($row[ColumnNames::BLACKOUT_INSTANCE_ID],
									Date::FromDatabase($row[ColumnNames::BLACKOUT_START]),
									Date::FromDatabase($row[ColumnNames::BLACKOUT_END]),
									$row[ColumnNames::RESOURCE_ID],
									$row[ColumnNames::USER_ID],
									$row[ColumnNames::SCHEDULE_ID],
									$row[ColumnNames::BLACKOUT_TITLE],
									$row[ColumnNames::BLACKOUT_DESCRIPTION],
									$row[ColumnNames::FIRST_NAME],
									$row[ColumnNames::LAST_NAME],
									$row[ColumnNames::RESOURCE_NAME],
									$row[ColumnNames::BLACKOUT_SERIES_ID]);
	}

	/**
	 * @return Date
	 */
	public function GetStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @return Date
	 */
	public function GetEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @return int
	 */
	public function GetResourceId()
	{
		return $this->resourceId;
	}

	/**
	 * @return int
	 */
	public function GetId()
	{
		return $this->instanceId;
	}

	/**
	 * @param Date $date
	 * @return bool
	 */
	public function OccursOn(Date $date)
	{
		return $this->date->OccursOn($date);
	}
}
