<?php
defined('_JEXEC') or die;

interface IResourceAvailabilityStrategy
{
	/**
	 * @param Date $startDate
	 * @param Date $endDate
	 * @return array|IReservedItem[]
	 */
	public function getItemsBetween(RFDate $startDate, RFDate $endDate);
}