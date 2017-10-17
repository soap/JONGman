<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFReservationItem implements IReservedItem
{
	/**
	 * @var int
	 */
	public $instanceId;
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
	public $fullName;
	
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
	 * @param $instanceId int
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
	 * @param $userName string;
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
		$instanceId = null,
		$referenceNumber = null,
		$startDate = null,
		$endDate = null,
		$resourceName = null,
		$resourceId = null,
		$reservationId = null,
		$title = null,
		$description = null,
		$scheduleId = null,
		$userName = null,
		//$userFirstName = null,
		//$userLastName = null,
		$userId = null,
		$userLevelId = null,
		//$userPhone = null,
		//$userOrganization = null,
		//$userPosition = null,
		$participant_list = null,
		$invitee_list = null
	)
	{
		$this->instanceId = $instanceId;
		$this->referenceNumber = $referenceNumber;
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->resourceName = $resourceName;
		$this->resourceId = $resourceId;
		$this->reservationId = $reservationId;
		$this->title = $title;
		$this->description = $description;
		$this->scheduleId = $scheduleId;
		$this->fullName = $userName;
		//$this->firstName = $userFirstName;
		//$this->ownerFirstName = $userFirstName;
		//$this->lastName = $userLastName;
		//$this->ownerLastName = $userLastName;
		//$this->ownerPhone = $userPhone;
		//$this->ownerOrganization = $userOrganization;
		//$this->ownerPosition = $userPosition;
		$this->userId = $userId;
		$this->userLevelId = $userLevelId;

		if (!empty($startDate) && !empty($endDate))
		{
			$this->date = new RFDateRange($startDate, $endDate);
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
		$view = new RFReservationItem (
			$row->instance_id,
			$row->reference_number,
			RFDate::fromDatabase($row->start_date),
			RFDate::fromDatabase($row->end_date),
			$row->resource_title,
			$row->resource_id,
			$row->reservation_id,
			$row->reservation_title,
			$row->reservation_description,
			$row->schedule_id,
			$row->owner_name,
			//$row->owner_firstname,
			//$row->owner_lastname,
			$row->owner_id,
			$row->user_level,
			//$row->owner_phone,
			//$row->owner_unit,
			//$row->owner_position,
			$row->participant_list,
			$row->invitee_list
		);

		if (isset($row->created))
		{
			$view->createdDate = RFDate::fromDatabase($row->created);
			$view->dateCreated = RFDate::fromDatabase($row->created);
		}

		if (isset($row->modified))
		{
			$view->modifiedDate = RFDate::fromDatabase($row->modified);
		}

		if (isset($row->repeat_type))
		{
			$repeatConfig = new JRegistry();
			$repeatConfig->loadString($row->repeat_options);

			$view->repeatType = $row->repeat_type;
			$view->repeatInterval = $repeatConfig->get('interval');
			$view->repeatWeekdays = $repeatConfig->get('weekdays');
			$view->repeatMonthlyType = $repeatConfig->get('monthlyType');
			$view->repeatTerminationDate = $repeatConfig->get('terminationDate');

			$view->isRecurring = ($row->repeat_type !== 'none');
		}

		if (isset($row->state))
		{
			$view->requiresApproval = $row->state == -1;
		}

		if (isset($row->owner_email))
		{
			$view->ownerEmailAddress = $row->owner_email;
		}

		if (isset($row->series_id))
		{
			$view->seriesId = $row->id;
		}

		return $view;
	}

	/**
	 * @param Date $date
	 * @return bool
	 */
	public function occursOn(RFDate $date)
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
