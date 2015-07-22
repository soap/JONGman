<?php
defined('_JEXEC') or die;

interface IBlackoutValidationResult
{
	/**
	 * @return bool
	 */
	public function wasSuccessful();

	/**
	 * @abstract
	 * @return string
	*/
	public function message();

	/**
	 * @abstract
	 * @return array|RFReservationItemView[]
	*/
	public function conflictingReservations();

	/**
	 * @abstract
	 * @return array|RFBlackoutItemView[]
	*/
	public function conflictingBlackouts();
}