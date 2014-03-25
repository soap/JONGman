<?php
defined ( '_JEXEC' ) or die ();
class RFEventCommandOwnerchanged extends RFEventCommand 
{
	/**
	 *
	 * @var OwnerChangedEvent
	 */
	private $event;
	
	public function __construct(RREventOwnerChanged $event) 
	{
		$this->event = $event;
	}
	
	public function execute($db = null) 
	{
		if ($db == null) $db = JFactory::getDbo();
		
		$oldOwnerId = $this->event->oldOwnerId();
		$newOwnerId = $this->event->newOwnerId();
		
		$instances = $this->event->series()->_instances ();
		
		$query = $db->getQuery(true);
		foreach ( $instances as $instance ) {
			if (! $instance->isNew ()) {
				$id = $instance->reservationId ();
				$userIds = array($oldOwnerId, $newOwnerId);
				$query->clear();
				$query->delete('#__jongman_reservation_users')
					->where('reservation_id ='.$id)
					->where('user_id IN ('.implode(',', $userIds).')');
				$db->setQuery($query);
				$db->execute();
				
				$obj = new StdClass();
				$obj->reservation_id = $id;
				$obj->user_id = $newOwnerId;
				$obj->user_level = 1;
				$db->insertObject('#__jongman_reservation_users', $obj);
			}
		}
	}
}