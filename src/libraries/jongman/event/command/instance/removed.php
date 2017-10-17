<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFEventCommandInstanceRemoved extends RFEventCommand
{	
	/**
	 * @var Reservation
	 */
	private $instance;

	public function __construct(RFReservation $instance, RFReservationSeries $series)
	{
		$this->instance = $instance;
		$this->series = $series;
	}

	public function execute($dbo)
	{
		$table = JTable::getInstance('Instance', 'JongmanTable');
		if ($table->load(array('reference_number' => $this->instance->referenceNumber()) ))
		{
			$table->delete();
		}
	}
}