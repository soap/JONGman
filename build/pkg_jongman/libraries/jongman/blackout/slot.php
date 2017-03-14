<?php
defined('_JEXEC') or die;

class RFBlackoutSlot implements IReservationSlot
{
	/**
	 * @var Date
	 */
	protected $begin;

	/**
	 * @var Date
	 */
	protected $end;

	/**
	 * @var Date
	 */
	protected $displayDate;

	/**
	 * @var int
	 */
	protected $periodSpan;

	/**
	 * @var BlackoutItemView
	 */
	private $blackout;

	/**
	 * @var string
	 */
	protected $beginSlotId;

	/**
	 * @var string
	 */
	protected $endSlotId;

	/**
	 * @var SchedulePeriod
	 */
	protected $_beginPeriod;

	/**
	 * @var SchedulePeriod
	 */
	protected $_endPeriod;

	/**
	 * @param RFSchedulePeriod $begin
	 * @param RFSchedulePeriod $end
	 * @param RFDate $displayDate
	 * @param int $periodSpan
	 * @param RFBlackoutItem $blackout
	 */
	public function __construct(RFSchedulePeriod $begin, RFSchedulePeriod $end, RFDate $displayDate, $periodSpan, RFBlackoutItem $blackout)
	{
		$this->blackout = $blackout;
		$this->begin = $begin->beginDate();
		$this->displayDate = $displayDate;
		$this->end = $end->endDate();
		$this->periodSpan = $periodSpan;
		$this->beginSlotId = $begin->id();
		$this->endSlotId = $end->id();

		$this->_beginPeriod = $begin;
		$this->_endPeriod = $end;
	}

	/**
	 * @return RFTime
	 */
	public function begin()
	{
		return $this->begin->getTime();
	}

	/**
	 * @return RFDate
	 */
	public function beginDate()
	{
		return $this->begin;
	}

	/**
	 * @return RFTime
	 */
	public function end()
	{
		return $this->end->getTime();
	}

	/**
	 * @return Date
	 */
	public function endDate()
	{
		return $this->end;
	}

	/**
	 * @return Date
	 */
	public function date()
	{
		return $this->displayDate;
	}

	/**
	 * @return int
	 */
	public function periodSpan()
	{
		return $this->periodSpan;
	}

	/**
	 * @return string
	 */
	public function label()
	{
		return $this->blackout->title;
	}

	public function isReservable()
	{
		return false;
	}

	public function isReserved()
	{
		return false;
	}

	public function isPending()
	{
		return false;
	}

	public function isPastDate(RFDate $date)
	{
		return $this->displayDate->setTime($this->begin())->lessThan($date);
	}

	public function toTimezone($timezone)
	{
		return new RFBlackoutSlot($this->_beginPeriod->toTimezone($timezone), $this->_endPeriod->toTimezone($timezone), $this->date(), $this->periodSpan(), $this->blackout);
	}

	public function isOwnedBy(JUser $user)
	{
		return false;
	}

	public function isParticipating(JUser $user)
	{
		return false;
	}

	public function beginSlotId()
	{
		return $this->beginSlotId;
	}

	public function endSlotId()
	{
		return $this->endSlotId;
	}

	public function color()
	{
		return null;
	}

	/**
	 * @return bool
	 */
	public function hasCustomColor()
	{
		return false;
	}

	/**
	 * @return string
	 */
	public function textColor()
	{
		return null;
	}
}