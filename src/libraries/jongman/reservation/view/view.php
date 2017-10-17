<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationView
{
	public $reservationId;
	public $seriesId;
	public $referenceNumber;
	public $resourceId;
	public $resourceName;
	public $scheduleId;
	public $statusId;
	/**
	 * @var Date
	 */
	public $startDate;
	/**
	 * @var Date
	 */
	public $endDate;
	/**
	 * @var Date
	 */
	public $dateCreated;
	/**
	 * @var Date
	 */
	public $dateModified;
	public $ownerId;
	public $ownerEmailAddress;
	public $ownerFirstName;
	public $ownerLastName;
	public $title;
	public $description;
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
	 * @var int[]
	 */
	public $additionalResourceIds = array();

	/**
	 * @var ReservationResourceView[]
	*/
	public $resources = array();

	/**
	 * @var ReservationUserView[]
	*/
	public $participants = array();

	/**
	 * @var ReservationUserView[]
	*/
	public $invitees = array();

	/**
	 * @var array|ReservationAccessoryView[]
	*/
	public $accessories = array();

	/**
	 * @var array|AttributeValue[]
	*/
	public $attributes = array();

	/**
	 * @var array|ReservationAttachmentView[]
	*/
	public $attachments = array();

	/**
	 * @var ReservationReminderView|null
	*/
	public $startReminder;

	/**
	 * @var ReservationReminderView|null
	 */
	public $endReminder;

	/**
	 * @param AttributeValue $attribute
	 */
	public function addAttribute(RFAttributeValue $attribute)
	{
		$this->attributes[$attribute->attributeId] = $attribute;
	}

	/**
	 * @param $attributeId int
	 * @return mixed
	 */
	public function getAttributeValue($attributeId)
	{
		if (array_key_exists($attributeId, $this->attributes))
		{
			return $this->attributes[$attributeId]->value;
		}

		return null;
	}

	/**
	 * @return bool
	 */
	public function isRecurring()
	{
		return $this->repeatType != RFRepeatType::None;
	}

	/**
	 * @return bool
	 */
	public function isDisplayable()
	{
		return true; // some qualification should probably be made
	}

	/**
	 * @return bool
	 */
	public function requiresApproval()
	{
		return $this->statusId == RFReservationStatus::pending;
	}

	/**
	 * @param ReservationAttachmentView $attachment
	 */
	public function addAttachment(RFReservationAttachmentView $attachment)
	{
		$this->attachments[] = $attachment;
	}
}