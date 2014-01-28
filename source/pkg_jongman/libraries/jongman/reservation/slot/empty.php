<?php
defined('_JEXEC') or die;
jimport('jongman.application.schedule.ireservationslot');

//require_once(ROOT_DIR . 'Domain/Values/ReservationStartTimeConstraint.php');


class RFReservationSlotEmpty implements IReservationSlot
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
	protected $_date;

	/**
	 * @var $_isReservable
	 */
	protected $_isReservable;

	/**
	 * @var int
	 */
	protected $_periodSpan;

	protected $_beginDisplayTime;
	protected $_endDisplayTime;

	protected $_beginSlotId;
	protected $_endSlotId;

	public function __construct(RFSchedulePeriod $begin, RFSchedulePeriod $end, RFDate $displayDate, $isReservable)
	{
		$this->_begin = $begin->beginDate();
		$this->_end = $end->endDate();
		$this->_date = $displayDate;
		$this->_isReservable = $isReservable;

		$this->_beginDisplayTime = $this->_begin->getTime();
		if (!$this->_begin->dateEquals($displayDate))
		{
			$this->_beginDisplayTime = $displayDate->getDate()->getTime();
		}

		$this->_endDisplayTime = $this->_end->getTime();
		if (!$this->_end->dateEquals($displayDate))
		{
			$this->_endDisplayTime = $displayDate->getDate()->getTime();
		}

		$this->_beginSlotId = $begin->Id();
		$this->_endSlotId = $end->Id();
	}

	/**
	 * @return Time
	 */
	public function begin()
	{
		return $this->_beginDisplayTime;
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
		return $this->_endDisplayTime;
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
		return $this->_date;
	}

	/**
	 * @return int
	 */
	public function periodSpan()
	{
		return 1;
	}

	public function label()
	{
		return '';
	}

	public function isReservable()
	{
		return $this->_isReservable;
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
		$constraint = '';

		if (empty($constraint))
		{
			$constraint = 'default';
		}

		if ($constraint == 'none')
		{
			return false;
		}

		if ($constraint == 'current')
		{
			return $this->_date->setTime($this->end(), true)->lessThan($date);
		}

		return $this->_date->setTime($this->begin())->lessThan($date);
	}

	public function toTimezone($timezone)
	{
		return new EmptyReservationSlot($this->beginDate()->toTimezone($timezone), $this->end()->toTimezone($timezone), $this->date(), $this->_isReservable);
	}

	public function isOwnedBy(JUser $user)
	{
		return false;
	}

	public function IsParticipating(JUser $user)
	{
		return false;
	}

	public function beginSlotId()
	{
		return $this->_beginSlotId;
	}

	public function endSlotId()
	{
		return $this->_endSlotId;
	}
}
