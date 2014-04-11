<?php
defined('_JEXEC') or die;

class RFEventCommandDeleteseries extends RFEventCommand
{
	public function __construct(RFReservationExistingSeries $series)
	{
		$this->series = $series;
	}
	
	public function execute($dbo = null)
	{
		if ($dbo == null) $dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		
		$query->delete('#__jongman_reservations')
			->where('id = '.$this->series->seriesId());
		$dbo->setQuery($query);
		
		return $dbo->execute();
	}
}

