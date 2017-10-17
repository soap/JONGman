<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
class RFReservationUserLevel
{
	public function __construct()
	{
	}

	const ALL = 0;
	const OWNER = 1;
	const PARTICIPANT = 2;
	const INVITEE = 3;
}