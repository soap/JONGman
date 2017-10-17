<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IReservationRepository
{
	/**
	 * @abstract
	 * @param Date $startDate
	 * @param Date $endDate
	 * @param int|null $userId
	 * @param int|ReservationUserLevel|null $userLevel
	 * @param int|null $scheduleId
	 * @param int|null $resourceId
	 * @return ReservationItemView[]
	 */
	public function getReservationList(
		RFDate $startDate,
		RFDate $endDate,
		$userId = null,  //ReservationViewRepository::ALL_USERS,
		$userLevel = 1,  //ReservationUserLevel::OWNER,
		$scheduleId = null, //ReservationViewRepository::ALL_SCHEDULES,
		$resourceId = null); //ReservationViewRepository::ALL_RESOURCES);

	/**
	 * @abstract
	 * @param Date $startDate
	 * @param Date $endDate
	 * @param string $accessoryName
	 * @return mixed
	 */
	public function getAccessoryReservationList(RFDate $startDate, RFDate $endDate, $accessoryName);

	/**
	 * @abstract
	 * @param DateRange $dateRange
	 * @param int|null $scheduleId
	 * @return BlackoutItemView[]
	 */
	public function getBlackoutsWithin(RFDateRange $dateRange, $scheduleId = null);

	/**
	 * @abstract
	 * @param DateRange $dateRange
	 * @return array|AccessoryReservation[]
	 */
	public function getAccessoriesWithin(RFDateRange $dateRange);
}