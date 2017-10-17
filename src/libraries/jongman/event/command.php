<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFEventCommand
{

	private $query;

	/**
	 * @var RFReservationExistingSeries
	 */
	protected $series;

	public function __construct($query, RFReservationExistingSeries $series)
	{
		$this->query = $query;
		$this->series = $series;
	}

	public function execute($dbo)
	{
		if (!$this->series->requiresNewSeries())
		{
			$dbo->setQuery($this->query);
			@$dbo->execute();
		}
	}
}