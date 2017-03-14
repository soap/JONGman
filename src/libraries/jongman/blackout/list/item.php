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
	public function buildSlot(RFSchedulePeriod $start, RFSchedulePeriod $end, RFDate $displayDate, $span)
	{
		return new RFBlackoutSlot($start, $end, $displayDate, $span, $this->item);
	}
}
?>