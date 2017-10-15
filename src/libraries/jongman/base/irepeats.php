<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IRepeatOptions
{
	/**
	 * Gets array of DateRange objects
	 *
	 * @param FRDateRange $startingDates
	 * @return array|DateRange[]
	 */
	function getDates(RFDateRange $startingDates);

	function configurationString();

	function repeatType();

	function equals(IRepeatOptions $repeatOptions);

	function hasSameConfigurationAs(IRepeatOptions $repeatOptions);

	function terminationDate();
}