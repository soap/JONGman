<?php
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