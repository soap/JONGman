<?php
defined('_JEXEC') or die;

class RFEventCommand
{
	/**
	 * @var JDatabaseQuery
	 */
	private $command;

	/**
	 * @var ExistingReservationSeries
	 */
	private $series;

	public function __construct(JDatabaseQuery $command, RFReservationExistingSeries $series)
	{
		$this->command = $command;
		$this->series = $series;
	}

	public function execute($dbo = null)
	{
		if ($dbo == null) {
			$dbo = JFactory::getDbo();
		}
		
		if (!$this->series->requiresNewSeries())
		{
			return $dbo->execute($this->command);
		}
	}
}