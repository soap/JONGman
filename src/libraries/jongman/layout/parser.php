<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFLayoutParser
{
	private $layout;
	private $timezone;

	public function __construct($timezone)
	{
		$this->layout = new RFLayoutSchedule($timezone);
		$this->timezone = $timezone;
	}

	public function addReservable($reservableSlots, $dayOfWeek = null)
	{
		$cb = array($this, 'appendPeriod');
		$this->parseSlots($reservableSlots, $dayOfWeek, $cb);
	}

	public function addBlocked($blockedSlots, $dayOfWeek = null)
	{
		$cb = array($this, 'appendBlocked');

		$this->parseSlots($blockedSlots, $dayOfWeek, $cb);
	}

	public function getLayout()
	{
		return $this->layout;
	}

	private function appendPeriod($start, $end, $label, $dayOfWeek = null)
	{
		$this->layout->appendPeriod(RFTime::parse($start, $this->timezone),
									RFTime::parse($end, $this->timezone),
									$label,
									$dayOfWeek);
	}

	private function appendBlocked($start, $end, $label, $dayOfWeek = null)
	{
		$this->layout->appendBlockedPeriod(RFTime::Parse($start, $this->timezone),
										   RFTime::Parse($end, $this->timezone),
										   $label,
										   $dayOfWeek);
	}

	private function parseSlots($allSlots, $dayOfWeek, $callback)
	{
		$lines = preg_split("/[\n]/", $allSlots, -1, PREG_SPLIT_NO_EMPTY);

		foreach ($lines as $slotLine)
		{
			$label = null;
			$parts = preg_split('/(\d?\d:\d\d\s*\-\s*\d?\d:\d\d)(.*)/', trim($slotLine), -1,
								PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
			$times = explode('-', $parts[0]);
			$start = trim($times[0]);
			$end = trim($times[1]);

			if (count($parts) > 1)
			{
				$label = trim($parts[1]);
			}

			call_user_func($callback, $start, $end, $label, $dayOfWeek);
		}
	}
}