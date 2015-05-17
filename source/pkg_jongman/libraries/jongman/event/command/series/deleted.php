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
		
		$query->clear();
		$query->delete()->from($database->quoteName('#__jongman_reservation_fields'));
		$query->where('reservation_id = '.$id);
		$query->where("field_key LIKE 'reservation_custom_fields.%'");
		$database->setQuery($query);
		
		if (!$database->execute()) {
			throw new Exception($database->getErrorMsg());
		}
		
	}
}