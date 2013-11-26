<?php
defined('_JEXEC') or die;
jimport('jongman.application.schedule.ireservationslot');

class ReservationSlot implements IReservationSlot
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
	public function __construct(SchedulePeriod $begin, SchedulePeriod $end, JMDate $displayDate, $periodSpan,
								ReservationItem $reservation)
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
			return SlotLabelFactory::Create($this->_reservation);
		}
		return $factory->Format($this->_reservation);
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
		return $this->_reservation->RequiresApproval;
	}

	public function isPastDate(JMDate $date)
	{
		return $this->_displayDate->setTime($this->begin())->lessThan($date);
	}

	public function toTimezone($timezone)
	{
		return new ReservationSlot(
			$this->beginDate()->toTimezone($timezone), 
			$this->endDate()->toTimezone($timezone), 
			$this->date(), $this->periodSpan(), $this->_reservation);
	}

	public function id()
	{
		return $this->_reservation->ReferenceNumber;
	}

	public function isOwnedBy(JUser $user)
	{
		return $this->_reservation->user_id == $user->get('id;');
	}

	public function IsParticipating(JUser $user)
	{
		$uid = JFactory::getUser();
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
