<?php
defined('_JEXEC') or die;
jimport('jongman.base.ireservationslot');
jimport('jongman.utils.slotlabelfactory');

class RFReservationSlot implements IReservationSlot
{
	/**
	 * @var Date
	 */
	protected $_begin;

	/**
	 * @var Date
	 */
	protected $_end;

	/**
	 * @var Date
	 */
	protected $_displayDate;

	/**
	 * @var int
	 */
	protected $_periodSpan;

	/**
	 * @var ReservationItemView
	 */
	private $_reservation;

	/**
	 * @var string
	 */
	protected $_beginSlotId;

	/**
	 * @var string
	 */
	protected $_endSlotId;

	/**
	 * @param SchedulePeriod $begin
	 * @param SchedulePeriod $end
	 * @param Date $displayDate
	 * @param int $periodSpan
	 * @param ReservationItemView $reservation
	 */
	public function __construct(RFSchedulePeriod $begin, RFSchedulePeriod $end, RFDate $displayDate, $periodSpan,
								RFReservationItem $reservation)
	{
		$this->_reservation = $reservation;
		$this->_begin = $begin->beginDate();
		$this->_displayDate = $displayDate;
		$this->_end = $end->endDate();
		$this->_periodSpan = $periodSpan;

		$this->_beginSlotId = $begin->Id();
		$this->_endSlotId = $end->Id();
	}

	/**
	 * @return Time
	 */
	public function begin()
	{
		return $this->_begin->getTime();
	}

	/**
	 * @return Date
	 */
	public function beginDate()
	{
		return $this->_begin;
	}

	/**
	 * @return Time
	 */
	public function end()
	{
		return $this->_end->getTime();
	}

	/**
	 * @return Date
	 */
	public function endDate()
	{
		return $this->_end;
	}

	/**
	 * @return Date
	 */
	public function date()
	{
		return $this->_displayDate;
	}

	/**
	 * @return int
	 */
	public function periodSpan()
	{
		return $this->_periodSpan;
	}

	public function label($factory = null)
	{
		if (empty($factory))
		{
			return RFSlotLabelFactory::create($this->_reservation);
		}
		return $factory->format($this->_reservation);
	}

	public function isReservable()
	{
		return false;
	}

	public function isReserved()
	{
		return true;
	}

	public function isPending()
	{
		return $this->_reservation->requiresApproval;
	}

	public function isPastDate(RFDate $date)
	{
		return $this->_displayDate->setTime($this->begin())->lessThan($date);
	}

	public function toTimezone($timezone)
	{
		return new RFReservationSlot(
			$this->beginDate()->toTimezone($timezone), 
			$this->endDate()->toTimezone($timezone), 
			$this->date(), $this->periodSpan(), $this->_reservation);
	}

	/**
	 * 
	 * get reservation Id
	 */
	public function getReservationId() 
	{
		return $this->_reservation->reservationId;	
	}
	
	/**
	 * 
	 * get reservation id (reference number)
	 * @deprecated 2.5
	 */	
	public function id() 
	{
		return $this->_reservation->referenceNumber;	
	}
	
	
	public function getInstanceId()
	{
		return $this->_reservation->instanceId;	
	}
	
	public function getReferenceNumber()
	{
		return $this->_reservation->referenceNumber;
	}

	public function isOwnedBy(JUser $user)
	{
		return $this->_reservation->userId == $user->get('id');
	}

	public function isParticipating(JUser $user)
	{
		if (empty($user)) {
			$user = JFactory::getUser();
		}
		$uid = $user->get('id');
		return $this->_reservation->isUserParticipating($uid) || $this->_reservation->isUserInvited($uid);
	}

	public function __toString()
	{
		return sprintf("Start: %s, End: %s, Span: %s", $this->Begin(), $this->End(), $this->PeriodSpan());
	}

	/**
	 * @return string
	 */
	public function beginSlotId()
	{
		return $this->_beginSlotId;
	}

	/**
	 * @return string
	 */
	public function endSlotId()
	{
		return $this->_beginSlotId;
	}
}
