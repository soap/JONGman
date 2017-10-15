<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

/**
 * 
 * Reservation instance
 * @author Prasit Gebsaap
 *
 */
class RFReservation
{
	/**
	 * @var string
	 */
	protected $referenceNumber;
	
	/**
	 * @var RFDate
	 */
	protected $startDate;

	/**
	 * @var RFDate
	 */
	protected $endDate;
	

	/**
	 * @return string
	 */
	public function referenceNumber()
	{
		return $this->referenceNumber;
	}

	/**
	 * @return RFDate
	 */
	public function startDate()
	{
		return $this->startDate;
	}

	/**
	 * @return RFDate
	 */
	public function endDate()
	{
		return $this->endDate;
	}

	/**
	 * @return RFDateRange
	 */
	public function duration()
	{
		return new RFDateRange($this->startDate(), $this->endDate());
	}

	protected $reservationId;

	public function reservationId()
	{
		return $this->reservationId;
	}

	/**
	 * @var array|int[]
	 */
	private $_participantIds = array();

	/**
	 * @var array|int[]
	 */
	protected $addedParticipants = array();

	/**
	 * @var array|int[]
	 */
	protected $removedParticipants = array();

	/**
	 * @var array|int[]
	 */
	protected $unchangedParticipants = array();

	/**
	 * @var int[]
	 */
	private $_inviteeIds = array();

	/**
	 * @var int[]
	 */
	protected $addedInvitees = array();

	/**
	 * @var int[]
	 */
	protected $removedInvitees = array();

	/**
	 * @var int[]
	 */
	protected $unchangedInvitees = array();


	/**
	 * @var ReservationSeries
	 */
	public $series;

	public function __construct(RFReservationSeries $reservationSeries, RFDateRange $reservationDate, $reservationId = null,
								$referenceNumber = null)
	{
		$this->series = $reservationSeries;

		$this->setReservationDate($reservationDate);
		$this->setReferenceNumber($referenceNumber);

		if (!empty($reservationId))
		{
			$this->setReservationId($reservationId);
		}

		if (empty($referenceNumber))
		{
			$this->setReferenceNumber(uniqid());
		}
	}

	public function setReservationId($reservationId)
	{
		$this->reservationId = $reservationId;
	}

	public function setReferenceNumber($referenceNumber)
	{
		$this->referenceNumber = $referenceNumber;
	}

	public function setReservationDate(RFDateRange $reservationDate)
	{
		$this->startDate = $reservationDate->getBegin();
		$this->endDate = $reservationDate->getEnd();
	}

	/**
	 * @internal
	 * @param array|int[] $participantIds
	 * @return void
	 */
	public function withParticipants($participantIds)
	{
		$this->_participantIds = $participantIds;
		$this->unchangedParticipants = $participantIds;
	}

	/**
	 * @param int $participantId
	 */
	public function withParticipant($participantId)
	{
		$this->_participantIds[] = $participantId;
		$this->unchangedParticipants[] = $participantId;
	}

	/**
	 * @internal
	 * @param array|int[] $inviteeIds
	 * @return void
	 */
	public function withInvitees($inviteeIds)
	{
		$this->_inviteeIds = $inviteeIds;
		$this->unchangedInvitees = $inviteeIds;
	}

	/**
	 * @param int $inviteeId
	 */
	public function withInvitee($inviteeId)
	{
		$this->_inviteeIds[] = $inviteeId;
		$this->unchangedInvitees[] = $inviteeId;
	}

	/**
	 * @param array|int[] $participantIds
	 * @return int
	 */
	public function changeParticipants($participantIds)
	{
		$diff = new RFArrayDiff($this->_participantIds, $participantIds);

		$this->addedParticipants = $diff->getAddedToArray1();
		$this->removedParticipants = $diff->getRemovedFromArray1();
		$this->unchangedParticipants = $diff->getUnchangedInArray1();

		$this->_participantIds = $participantIds;

		return count($this->addedParticipants) + count($this->removedParticipants);
	}

	/**
	 * @return array|int[]
	 */
	public function participants()
	{
		return $this->_participantIds;
	}

	/**
	 * @return array|int[]
	 */
	public function addedParticipants()
	{
		return $this->addedParticipants;
	}

	/**
	 * @return array|int[]
	 */
	public function removedParticipants()
	{
		return $this->removedParticipants;
	}

	/**
	 * @return array|int[]
	 */
	public function unchangedParticipants()
	{
		return $this->unchangedParticipants;
	}

	/**
	 * @return array|int[]
	 */
	public function addedInvitees()
	{
		return $this->addedInvitees;
	}

	/**
	 * @return array|int[]
	 */
	public function removedInvitees()
	{
		return $this->removedInvitees;
	}

	/**
	 * @return array|int[]
	 */
	public function unchangedInvitees()
	{
		return $this->unchangedInvitees;
	}

	/**
	 * @param array|int[] $inviteeIds
	 * @return int
	 */
	public function changeInvitees($inviteeIds)
	{
		/*$diff = new ArrayDiff($this->_inviteeIds, $inviteeIds);

		$this->addedInvitees = $diff->getAddedToArray1();
		$this->removedInvitees = $diff->getRemovedFromArray1();
		$this->unchangedInvitees = $diff->getUnchangedInArray1();

		$this->_inviteeIds = $inviteeIds;
		*/
		return count($this->addedInvitees) + count($this->removedInvitees);
	}

	/**
	 * @return bool
	 */
	public function isNew()
	{
		return $this->reservationId() == null;
	}

	/**
	 * @param int $inviteeId
	 * @return bool whether the invitation was accepted
	 */
	public function acceptInvitation($inviteeId)
	{
		if (in_array($inviteeId, $this->_inviteeIds))
		{
			$this->addedParticipants[] = $inviteeId;
			$this->removedInvitees[] = $inviteeId;

			return true;
		}

		return false;
	}

	/**
	 * @param int $inviteeId
	 * @return bool whether the invitation was declined
	 */
	public function declineInvitation($inviteeId)
	{
		if (in_array($inviteeId, $this->_inviteeIds))
		{
			$this->removedInvitees[] = $inviteeId;
			return true;
		}

		return false;
	}

	/**
	 * @param int $participantId
	 * @return bool whether the participant was removed
	 */
	public function cancelParticipation($participantId)
	{
		if (in_array($participantId, $this->_participantIds))
		{
			$this->removedParticipants[] = $participantId;
			return true;
		}

		return false;
	}

	static function compare(RFReservation $res1, RFReservation $res2)
	{
		return $res1->startDate()->compare($res2->startDate());
	}
}