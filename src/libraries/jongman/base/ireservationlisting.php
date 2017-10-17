<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

interface IDateReservationListing extends IResourceReservationListing
{
	/**
	 * @param int $resourceId
	 * @return IResourceReservationListing
	 */
	public function forResource($resourceId);
}

interface IResourceReservationListing
{
	/**
	 * @return int
	 */
	public function count();
	
	/**
	 * @return array|ReservationListItem[]
	 */
	public function reservations();
}

interface IReservationListing extends IResourceReservationListing
{
	/**
	 * @param Date $date
	 * @return IReservationListing
	 */
	public function onDate($date);

	/**
	 * @param int $resourceId
	 * @return IReservationListing
	 */
	public function forResource($resourceId);

	/**
	 * @abstract
	 * @param Date $date
	 * @param int $resourceId
	 * @return array|ReservationListItem[]
	 */
	public function onDateForResource(RFDate $date, $resourceId);
}

interface IMutableReservationListing extends IReservationListing
{
	/**
	 * @abstract
	 * @param ReservationItemView $reservation
	 * @return void
	 */
	public function add($reservation);

	/**
	 * @abstract
	 * @param BlackoutItemView $blackout
	 * @return void
	 */
	public function addBlackout($blackout);
}
?>