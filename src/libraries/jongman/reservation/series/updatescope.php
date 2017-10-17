<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationSeriesUpdatescope 
{
	private function __construct()
	{
	}

	const THISINSTANCE = 'this';
	const FULLSERIES = 'full';
	const FUTUREINSTANCES = 'future';

	public static function createStrategy($seriesUpdateScope)
	{
		switch ($seriesUpdateScope)
		{
			case self::THISINSTANCE :
				return new RFReservationSeriesUpdatescopeInstance();
				break;
			case self::FULLSERIES :
				return new RFReservationSeriesUpdatescopeFull();
				break;
			case self::FUTUREINSTANCES :
				return new RFReservationSeriesUpdatescopeFuture();
				break;
			default :
				throw new Exception('Unknown seriesUpdateScope requested; '.$seriesUpdateScope);
		}
	}

	/**
	 * @param string $updateScope
	 * @return bool
	 */
	public static function isValid($updateScope)
	{
		return $updateScope == self::FULLSERIES ||
				$updateScope == self::THISINSTANCE ||
				$updateScope == self::FUTUREINSTANCES;
	}		
}