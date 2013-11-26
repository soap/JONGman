<?php 
defined('_JEXEC') or die;
import ('jongman.date.date');

interface IReservedItem
{
	/**
	 * @abstract
	 * @return Date
	 */
	public function getStartDate();

	/**
	 * @abstract
	 * @return Date
	 */
	public function getEndDate();

	/**
	 * @abstract
	 * @return int
	 */
	public function getResourceId();

	/**
	 * @abstract
	 * @return int
	 */
	public function getId();

	/**
	 * @abstract
	 * @param Date $date
	 * @return bool
	 */
	public function occursOn(JMDate $date);
}

class ReservationItem implements IReservedItem
{
	/**
	 * @var string
	 */
	public $referenceNumber;

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
	 * @var string
	 */
	public $resourceName;

	/**
	 * @var int
	 */
	public $reservationId;

	/**
	 * @var int|ReservationUserLevel
	 */
	public $userLevelId;

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
	public $userId;

	/**
	 * @var null|Date
	 */
	public $createdDate;

	/**
	 * alias of $CreatedDate
	 * @var null|Date
	 */
	public $dateCreated;

	/**
	 * @var null|Date
	 */
	public $modifiedDate;

	/**
	 * @var null|bool
	 */
	public $isRecurring;

	/**
	 * @var null|bool
	 */
	public $requiresApproval;

	/**
	 * @var string|RepeatType
	 */
	public $repeatType;

	/**
	 * @var int
	 */

	public $repeatInterval;
	/**
	 * @var array
	 */

	public $repeatWeekdays;
	/**
	 * @var string|RepeatMonthlyType
	 */

	public $repeatMonthlyType;
	/**
	 * @var Date
	 */

	public $repeatTerminationDate;

	/**
	 * @var string
	 */
	public $ownerEmailAddress;
	/**
	 * @var string
	 */
	public $ownerPhone;
	/**
	 * @var string
	 */
	public $ownerOrganization;
	/**
	 * @var string
	 */
	public $ownerPosition;

	/**
	 * @var int
	 */
	public $seriesId;

	/**
	 * @var array|int[]
	 */
	public $participantIds = array();

	/**
	 * @var array|int[]
	 */
	public $inviteeIds = array();

	/**
	 * @param $referenceNumber string
	 * @param $startDate Date
	 * @param $endDate Date
	 * @param $resourceName string
	 * @param $resourceId int
	 * @param $reservationId int
	 * @param $userLevelId int|ReservationUserLevel
	 * @param $title string
	 * @param $description string
	 * @param $scheduleId int
	 * @param $userFirstName string
	 * @param $userLastName string
	 * @param $userId int
	 * @param $userPhone string
	 * @param $userPosition string
	 * @param $userOrganization string
	 * @param $participant_list string
	 * @param $invitee_list string
	 */
	public function __construct(
		$referenceNumber = null,
		$startDate = null,
		$endDate = null,
		$resourceName = null,
		$resourceId = null,
		$reservationId = null,
		$title = null,
		$description = null,
		$scheduleId = null,
		$userFirstName = null,
		$userLastName = null,
		$userId = null,
		$userPhone = null,
		$userOrganization = null,
		$userPosition = null,
		$participant_list = null,
		$invitee_list = null
	)
	{

		$this->referenceNumber = $referenceNumber;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->resourceName = $resourceName;
		$this->resourceId = $resourceId;
		$this->reservationId = $reservationId;
		$this->title = $title;
		$this->description = $description;
		$this->scheduleId = $scheduleId;
		$this->firstName = $userFirstName;
		$this->ownerFirstName = $userFirstName;
		$this->lastName = $userLastName;
		$this->ownerLastName = $userLastName;
		$this->ownerPhone = $userPhone;
		$this->ownerOrganization = $userOrganization;
		$this->ownerPosition = $userPosition;
		$this->userId = $userId;
		$this->userLevelId = $userLevelId;

		if (!empty($startDate) && !empty($endDate))
		{
			$this->date = new DateRange($startDate, $endDate);
		}

		if (!empty($participant_list))
		{
			$this->participantIds = explode(',', $participant_list);
		}

		if (!empty($invitee_list))
		{
			$this->inviteeIds = explode(',', $invitee_list);
		}
	}

	/**
	 * @static
	 * @param $row array
	 * @return ReservationItemView
	 */
	public static function populate($row)
	{
		$view = new ReservationItem (
			$row->alias,
			JMDate::fromDatabase($row->start_date),
			JMDate::fromDatabase($row->end_date),
			$row->resource_title,
			$row->resource_id,
			$row->id,
			$row->title,
			$row->description,
			$row->schedule_id,
			$row->owner_firstname,
			$row->owner_lastname,
			$row->owner_user_id,
			$row->owner_phone,
			$row->owner_unit,
			$row->owner_position,
			$row->participant_list,
			$row->invitee_list
		);

		if (isset($row->created))
		{
			$view->createdDate = JMDate::fromDatabase($row->created);
			$view->dateCreated = JMDate::fromDatabase($row->created);
		}

		if (isset($row->modified))
		{
			$view->modifiedDate = JMDate::fromDatabase($row->modified);
		}

		if (isset($row->repeat_type))
		{
			$repeatConfig = RepeatConfiguration::Create($row[ColumnNames::REPEAT_TYPE],
														$row[ColumnNames::REPEAT_OPTIONS]);

			$view->repeatType = $repeatConfig->type;
			$view->repeatInterval = $repeatConfig->interval;
			$view->repeatWeekdays = $repeatConfig->weekdays;
			$view->repeatMonthlyType = $repeatConfig->monthlyType;
			$view->repeatTerminationDate = $repeatConfig->terminationDate;

			$view->isRecurring = ($row->repeat_type != 0);
		}

		if (isset($row->state))
		{
			$view->requiresApproval = $row-> state == 1;
		}

		if (isset($row->owner_email))
		{
			$view->ownerEmailAddress = $row->owner_email;
		}

		if (isset($row->series_id))
		{
			$view->seriesId = $row->series_id;
		}

		return $view;
	}

	/**
	 * @param Date $date
	 * @return bool
	 */
	public function occursOn(Date $date)
	{
		return $this->date->occursOn($date);
	}

	/**
	 * @return Date
	 */
	public function getStartDate()
	{
		return $this->startDate;
	}

	/**
	 * @return Date
	 */
	public function getEndDate()
	{
		return $this->endDate;
	}

	/**
	 * @return int
	 */
	public function getReservationId()
	{
		return $this->reservationId;
	}

	/**
	 * @return int
	 */
	public function getResourceId()
	{
		return $this->resourceId;
	}

	/**
	 * @return string
	 */
	public function getReferenceNumber()
	{
		return $this->referenceNumber;
	}

	public function getId()
	{
		return $this->getReservationId();
	}

	/**
	 * @return DateDiff
	 */
	public function getDuration()
	{
		return $this->startDate->getDifference($this->endDate);
	}

	public function isUserOwner($userId)
	{
		return $this->userId == $userId;
	}

	/**
	 * @param $userId int
	 * @return bool
	 */
	public function isUserParticipating($userId)
	{
		return in_array($userId, $this->participantIds);
	}

	/**
	 * @param $userId int
	 * @return bool
	 */
	public function isUserInvited($userId)
	{
		return in_array($userId, $this->inviteeIds);
	}
}

class BlackoutItem implements IReservedItem
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
