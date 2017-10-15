<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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