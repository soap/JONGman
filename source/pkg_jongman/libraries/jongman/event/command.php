<?php
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
			$dbo->setQuery($query);
			@$dbo->execute($this->query);
		}
	}
}