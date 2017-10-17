<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
jimport('jongman.base.iblackoutvalidationresult');

class RFBlackoutValidationResult implements IBlackoutValidationResult
{
	/**
	 * @var array|BlackoutItemView[]
	 */
	private $conflictingBlackouts;

	/**
	 * @var array|ReservationItemView[]
	 */
	private $conflictingReservations;

	/**
	 * @param array|BlackoutItemView[] $conflictingBlackouts
	 * @param array|ReservationItemView[] $conflictingReservations
	 */
	public function __construct($conflictingBlackouts, $conflictingReservations)
	{
		$this->conflictingBlackouts = $conflictingBlackouts;
		$this->conflictingReservations = $conflictingReservations;
	}

	public function wasSuccessful()
	{
		return $this->canBeSaved();
	}

	/**
	 * @return bool
	 */
	public function canBeSaved()
	{
		return empty($this->conflictingBlackouts) && empty($this->conflictingReservations);
	}

	public function message()
	{
		return null;
	}

	/**
	 * @return array|RFReservationItemView[]
	 */
	public function conflictingReservations()
	{
		return $this->conflictingReservations;
	}

	/**
	 * @return array|RFBlackoutItemView[]
	 */
	public function conflictingBlackouts()
	{
		return $this->conflictingBlackouts;
	}
}