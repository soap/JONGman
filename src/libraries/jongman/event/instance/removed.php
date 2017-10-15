<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFEventInstanceRemoved extends RFSeriesEvent
{
	/**
	 * @var Reservation
	 */
	private $instance;

	/**
	 * @return Reservation
	 */
	public function getInstance()
	{
		return $this->instance;
	}

	public function __construct(RFReservation $reservationInstance, RFReservationExistingSeries $series)
	{
		$this->instance = $reservationInstance;
		parent::__construct($series, RFEventPriority::Highest);
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->instance->referenceNumber());
	}
}