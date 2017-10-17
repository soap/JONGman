<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationReminder
{
	private $value;
	private $interval;
	private $minutesPrior;

	public function __construct($value, $interval)
	{
		$this->value = $value;
		$this->interval = $interval;

		if ($interval == RFReservationReminderInterval::Days)
		{
			$this->minutesPrior = $value * 60 * 24;
		}
		elseif ($interval == RFReservationReminderInterval::Hours)
		{
			$this->minutesPrior = $value * 60;
		}
		else
		{
			$this->interval = RFReservationReminderInterval::Minutes;
			$this->minutesPrior = $value;
		}
	}

	public static function none()
	{
		return new RFReservationReminderNull();
	}

	public function enabled()
	{
		return true;
	}

	public function minutesPrior()
	{
		return $this->minutesPrior;
	}

	/**
	 * @param int $minutes
	 * @return ReservationReminder
	 */
	public static function fromMinutes($minutes)
	{
		if ($minutes % 1440 == 0)
		{
			return new RFReservationReminder($minutes / 1440, RFReservationReminderInterval::Days);
		}
		elseif ($minutes % 60 == 0)
		{
			return new RFReservationReminder($minutes / 60, RFReservationReminderInterval::Hours);
		}
		else
		{
			return new RFReservationReminder($minutes, RFReservationReminderInterval::Minutes);
		}
	}
}