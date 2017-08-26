<?php
defined('_JEXEC') or die;

class RFReservationStarttimeConstraint
{
	const _DEFAULT = 'future';
	const FUTURE = 'future';
	const CURRENT = 'current';
	const NONE = 'none';

	/**
	 * @static
	 * @param string $startTimeConstraint
	 * @return bool
	 */
	public static function isCurrent($startTimeConstraint)
	{
		return strtolower($startTimeConstraint) == self::CURRENT;
	}

	/**
	 * @static
	 * @param string $startTimeConstraint
	 * @return bool
	 */
	public static function isNone($startTimeConstraint)
	{
		return strtolower($startTimeConstraint) == self::NONE;
	}

	/**
	 * @static
	 * @param string $startTimeConstraint
	 * @return bool
	 */
	public static function ysFuture($startTimeConstraint)
	{
		return strtolower($startTimeConstraint) == self::FUTURE;
	}
}
