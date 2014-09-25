<?php
defined('_JEXEC') or die;


interface IDailyLayoutFactory
{
	/**
	 * @param IReservationListing $listing
	 * @param IScheduleLayout $layout
	 * @return IDailyLayout
	 */
	function Create(IReservationListing $listing, IScheduleLayout $layout);
}

class DailyLayoutFactory implements IDailyLayoutFactory
{
	public static function create(IReservationListing $listing, IScheduleLayout $layout)
	{
		return new RFLayoutDaily($listing, $layout);
	}
}

