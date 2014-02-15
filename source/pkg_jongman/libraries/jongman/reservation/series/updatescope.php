<?php
defined('_JEXEC') or die;

class RFReservationUpdatescope 
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
			case seriesUpdateScope::THISINSTANCE :
				return new RFReservationSeriesUpdatescopeInstance();
				break;
			case seriesUpdateScope::FULLSERIES :
				return new RFReservationSeriesUpdatescopeFull();
				break;
			case seriesUpdateScope::FUTUREINSTANCES :
				return new RFReservationSeriesUpdatescopeFuture();
				break;
			default :
				throw new Exception('Unknown seriesUpdateScope requested');
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