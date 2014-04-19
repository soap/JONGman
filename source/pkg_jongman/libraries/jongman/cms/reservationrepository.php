<?php
defined('_JEXEC') or die;

jimport('jongman.base.ireservationrepository');

class RFReservationRepository implements IReservationRepository
{
	/**
	 * (non-PHPdoc)
	 * @see IReservationViewRepository::getReservationList()
	 */
	public function getReservationList(RFDate $startDate, RFDate $endDate, $userId = null, $userLevel = 1, $scheduleId = null, $resourceId = null)
	{
		$config = array('ignore_request'=>true);
		
		$model = JModelLegacy::getInstance('Reservations', 'JongmanModel', $config);
		$tz = JongmanHelper::getUserTimezone();
		$model->setState('filter.start_date', $startDate->toTimezone($tz)->format('Y-m-d H:i:s'));
		$model->setState('filter.end_date', $endDate->toTimezone($tz)->format('Y-m-d H:i:s'));
		
		if (!empty($scheduleId)) {		
			$model->setState('filter.schedule_id', $scheduleId);
		}
		if (!empty($resourceId)) {
			$model->setState('filter.resource_id', $value);
		}
		
		$model->setState('filter.type_id', 1);
		$items = $model->getItems();
		
		$list = new RFReservationListing($tz);
		
		foreach($items as $item) {
			//add reservation first
			$reservationItem = RFReservationItem::populate($item);
			$list->add($reservationItem);	
		}
		
		return $list;
	}
	
	public function getAccessoryReservationList(RFDate $startDate, RFDate $endDate, $accessoryName)
	{
		
	}
	
	public function getBlackoutsWithin(RFDateRange $dateRange, $scheduleId = null)
	{
		
	}
	
	public function getAccessoriesWithin(RFDateRange $dateRange)
	{
		
	}
}