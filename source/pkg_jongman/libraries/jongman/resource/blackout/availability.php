<?php
defined('_JEXEC') or die;

class RFResourceBlackoutAvailability implements IResourceAvailabilityStrategy
{
	/**
	 * @var JModel
	 */
	protected $_repository;

	public function __construct($repository)
	{
		$this->_repository = $repository;
	}

	public function getItemsBetween(RFDate $startDate, RFDate $endDate)
	{
		$model = JModelLegacy::getInstance('Reservations', 'JongmanModel', array('ignore_request'=>true));
		$tz = JongmanHelper::getUserTimezone();
		$model->setState('filter.start_date', $startDate->toTimezone($tz)->format('Y-m-d H:i:s'));
		$model->setState('filter.end_date', $endDate->toTimezone($tz)->format('Y-m-d H:i:s'));
		$model->setState('filter.type', 2);
		$rows = $model->getItems();
		$reservations = array();
		foreach ($rows as $row) {
			$reservations[] = RFReservationItem::populate($row);
		}

		return $reservations;
	}
}