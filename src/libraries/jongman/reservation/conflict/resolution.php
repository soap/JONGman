<?php
defined('_JEXEC') or die;

interface IReservationConflictResolution
{
	/**
	 * @abstract
	 * @param ReservationItemView $existingReservation
	 * @return bool
	 */
	public function handle(RFReservationItemView $existingReservation);
}

abstract class RFReservationConflictResolution implements IReservationConflictResolution
{
	const Delete = 'delete';
	const Notify = 'notify';

	protected function __construct()
	{
	}

	/**
	 * @param string|RFReservationConflictResolution $resolutionType
	 * @return RFReservationConflictResolution
	 */
	public static function create($resolutionType)
	{
		if ($resolutionType == self::Delete)
		{
			return new RFReservationConflictDelete(new ReservationRepository());
		}
		return new RFReservationConflictNotify();
	}
}