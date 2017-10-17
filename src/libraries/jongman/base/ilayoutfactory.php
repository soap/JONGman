<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
interface ILayoutFactory
{
	/**
	 * @return IScheduleLayout
	 */
	public function createLayout();
}