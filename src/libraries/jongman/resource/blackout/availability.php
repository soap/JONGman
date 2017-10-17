<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFResourceBlackoutAvailability implements IResourceAvailabilityStrategy
{
	/**
	 * @var JModel
	 */
	protected $_repository;

	/**
	 * 
	 * Constructor, pass Model as repository
	 * @param unknown_type $repository
	 */
	public function __construct($repository)
	{
		$this->_repository = $repository;
	}

	/**
	 * (non-PHPdoc)
	 * @see IResourceAvailabilityStrategy::getItemsBetween()
	 */
	public function getItemsBetween(RFDate $startDate, RFDate $endDate)
	{
		$model = JModelLegacy::getInstance('Reservations', 'JongmanModel', array('ignore_request'=>true));
		$tz = RFApplicationHelper::getUserTimezone();
		$model->setState('filter.start_date', $startDate->toTimezone($tz)->format('Y-m-d H:i:s'));
		$model->setState('filter.end_date', $endDate->toTimezone($tz)->format('Y-m-d H:i:s'));
		$model->setState('filter.type_id', 2); //blackout
		$rows = $model->getItems();
		$reservations = array();
		foreach ($rows as $row) {
			$reservations[] = RFReservationItem::populate($row);
		}
		JLog::add('#blackout items between '.$model->getState('filter.start_date'). ' and '.$model->getState('filter.end_date').' is '.count($rows),
			JLog::DEBUG, 'validation');
		return $reservations;
	}
}