<?php
defined('_JEXEC') or die;

class ReservationListItem
{
	/**
	 * @var IReservedItemView
	 */
	protected $item;
	
	public function __construct(IReservedItem $reservedItem)
	{
		$this->item = $reservedItem;
	}

	/**
	 * @return Date
	 */
	public function startDate()
	{
		return $this->item->getStartDate();
	}

	/**
	 * @return Date
	 */
	public function endDate()
	{
		return $this->item->getEndDate();
	}

	public function occursOn(JMDate $date)
	{
		return $this->item->occursOn($date);
	}

	/**
	 * @param SchedulePeriod $start
	 * @param SchedulePeriod $end
	 * @param Date $displayDate
	 * @param int $span
	 * @return IReservationSlot
	 */
	public function buildSlot(SchedulePeriod $start, SchedulePeriod $end, JMDate $displayDate, $span)
	{
		return new ReservationSlot($start, $end, $displayDate, $span, $this->item);
	}

	/**
	 * @return int
	 */
	public function resourceId()
	{
		return $this->item->retResourceId();
	}

	/**
	 * @return int
	 */
	public function id()
	{
		return $this->item->getId();
	}
}

class BlackoutListItem extends ReservationListItem
{
	/**
	 * @param SchedulePeriod $start
	 * @param SchedulePeriod $end
	 * @param Date $displayDate
	 * @param int $span
	 * @return IReservationSlot
	 */
	public function buildSlot(SchedulePeriod $start, SchedulePeriod $end, JMDate $displayDate, $span)
	{
		return new BlackoutSlot($start, $end, $displayDate, $span, $this->item);
	}
}
?>