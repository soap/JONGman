<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationListItem
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

	public function occursOn(RFDate $date)
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
	public function buildSlot(RFSchedulePeriod $start, RFSchedulePeriod $end, RFDate $displayDate, $span)
	{
		return new RFReservationSlot($start, $end, $displayDate, $span, $this->item);
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
