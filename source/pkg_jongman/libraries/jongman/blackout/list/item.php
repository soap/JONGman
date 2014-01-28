<?php
defined('_JEXEC') or die;


class RFBlackoutListItem extends RFReservationListItem
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