<?php
defined('_JEXEC') or die;

class RFEventCommandSeriesDeleted extends RFEventCommand
{
	public function __construct(RFReservationExistingSeries $series)
	{
		$this->series = $series;
	}
	
	public function execute($database)
	{
		$id = $this->series->seriesId();
	
		$query = $database->getQuery(true);
		$query->delete('#__jongman_reservation_instances')
			->where('reservation_id='.$id);
		$database->setQuery($query);
		$database->query();
	
		$table = JTable::getInstance('Reservation', 'JongmanTable');
		$table->delete($id);
	}
}