<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined ( '_JEXEC' ) or die ();
class RFEventCommandOwnerChanged extends RFEventCommand 
{
	/**
	 *
	 * @var RFEventOwnerChanged
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
		
		$instances = $this->event->series()->_instances();
		
		$query = $db->getQuery(true);
		foreach ( $instances as $instance ) {
			if ( !$instance->isNew() ) {
				$reservationId = $instance->reservationId();
				//remove old owner and new owner from list
				$query->clear();
				$query->delete('#__jongman_reservation_users')
					->where('reservation_id = '.$reservationId)
					->where('(user_id = '.$oldOwnerId.') OR (user_id = '.$newOwnerId.')');
				$db->setQuery($query);
				$db->execute();
				
				//insert new owner
				$database->Execute(new RemoveReservationUserCommand($id, $newOwnerId));
				$query->clear();
				$query->insert('#__jongman_reservation_users')
					->set('reservation_id ='.$reservation_id)
					->set('user_id = '.$newOwnerId)
					->set('user_level = 1');
			
				$db->setQuery($query);
				$db->execute();
			}
		}
		
		return true;
	}
}