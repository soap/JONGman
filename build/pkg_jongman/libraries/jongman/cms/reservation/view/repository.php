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
		$tz = RFApplicationHelper::getUserTimezone();
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
		if ($scheduleId == null) $scheduleId = -1;
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$startDate = $dbo->quote($dateRange->getBegin()->toDatabase());
		$endDate = $dbo->quote($dateRange->getEnd()->toDatabase());
		
		$query->select('bs.*, bs.created_by AS owner_id, bi.id as instance_id, bi.start_date, bi.end_date, bi.blackout_id')
			->from('#__jongman_blackout_instances AS bi')
			->join('INNER', '#__jongman_blackouts AS bs ON bi.blackout_id = bs.id')
			->select('bsr.resource_id')
			->join('INNER', '#__jongman_blackout_resources AS bsr ON  bi.blackout_id = bsr.blackout_id')
			->select('r.title AS resource_name, r.schedule_id AS schedule_id')
			->join('INNER', '#__jongman_resources AS r ON bsr.resource_id = r.id')
			->select('u.name as author_name')
			->join('INNER', '#__users AS u ON u.id = bs.created_by')
			->where('
			(
				(bi.start_date >='.$startDate.' AND bi.start_date <= '.$endDate.')
				OR
				(bi.end_date >= '.$startDate.' AND bi.end_date <= '.$endDate.')
				OR
				(bi.start_date <= '.$startDate.' AND bi.end_date >='.$endDate.')
			)');
		$query->where('('.$scheduleId.' = -1 OR r.schedule_id = '.$scheduleId.')');
		$query->order('bi.start_date ASC');
		$dbo->setQuery($query);
		
		$rows = $dbo->loadObjectList();
		
		$blackouts = array();
		foreach ($rows as $row)
		{
			$blackouts[] = RFBlackoutItem::populate($row);
		}
		
		return $blackouts;		
	}
	
	public function getAccessoriesWithin(RFDateRange $dateRange)
	{
		
	}
}