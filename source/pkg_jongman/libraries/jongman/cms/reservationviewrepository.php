<?php
defined('_JEXEC') or die;

jimport('jongman.base.ireservationviewrepository');

class RFReservationViewRepository implements IReservationViewRepository
{
	/**
	 * (non-PHPdoc)
	 * @see IReservationRepository::getReservationList()
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
			$model->setState('filter.resource_id', $resourceId);
		}
		
		if (!empty($userId)) {
			$model->setState('filter.user_id', $userId);	
		}
		if (!empty($userLevel)) {
			$model->setState('filter.user_level', $userLevel);
		}
		
		$model->setState('filter.type_id', 1);
		$items = $model->getItems();

		$reservations = array();
		
		foreach($items as $item) {
			$reservations[] = RFReservationItem::populate($item);	
		}
		
		return $reservations;
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