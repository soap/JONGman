<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFEventCommandInstanceaAdded extends RFEventCommand 
{
	/**
	 * @var Reservation
	 */
	private $instance;
	
	/**
	 * @var ReservationSeries
	 */
	private $series;
	
	public function __construct(RFReservation $instance, RFReservationSeries $series)
	{
		$this->instance = $instance;
		$this->series = $series;
	}
	
	public function execute($dbo = null)
	{
		if ($dbo == null) $dbo = JFactory::getDbo();
		
		$instance = new StdClass();
		$instance->reservation_id = $this->series->seriesId();
		$instance->reference_number = $this->instance->referenceNumber();
		$instance->start_date = $this->instance->startDate()->toDatabase();
		$instance->end_date = $this->instance->endDate()->toDatabase();
		$dbo->insertObject('#__jongman_reservation_instances', $instance, 'id');
			
		$reservationId = $dbo->insertid();
	
		// we should add user and participants
	}	
}