<?php
defined('_JEXEC') or die;

jimport('jongman.base.iresourceavailabilitystrategy');
class RFResourceReservationAvailability implements IResourceAvailabilityStrategy
{
	/**
	 * @var JModel
	 */
	protected $_repository;

	public function __construct($repository)
	{
		$this->_repository = $repository;
	}
	
	/**
	 * 
	 * get reservation items between dates
	 * @param RFDate $startDate
	 * @param RFDate_type $endDate
	 * @return array of RFReservationItem
	 */
	public function getItemsBetween(RFDate $startDate, RFDate $endDate)
	{
		$model = JModelLegacy::getInstance('Reservations', 'JongmanModel', array('ignore_request'=>true));
		$tz = JongmanHelper::getUserTimezone();
		$model->setState('filter.start_date', $startDate->toTimezone($tz)->format('Y-m-d H:i:s'));
		$model->setState('filter.end_date', $endDate->toTimezone($tz)->format('Y-m-d H:i:s'));
		$model->setState('filter.type', 1);
		$rows = $model->getItems();
		$reservations = array();
		foreach ($rows as $row) {
			$reservations[] = RFReservationItem::populate($row);
		}

		return $reservations;
					
	}
}