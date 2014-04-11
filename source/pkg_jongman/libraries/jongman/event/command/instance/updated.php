<?php
defined('_JEXEC') or die;

class RFEventCommandInstanceUpdated extends RFEventCommand
{
	/**
	 * @var Reservation
	 */
	private $instance;
	
	/**
	 * @var ExistingReservationSeries
	 */
	private $series;
	
	public function __construct(RFReservation $instance, RFReservationExistingSeries $series)
	{
		$this->instance = $instance;
		$this->series = $series;
	}
	
	public function execute($dbo = null)
	{
		$instanceId = $this->instance->reservationId();
		$table = JTable::getInstance('Instance', 'JongmanTable');
		$keys = array('reference_number'=>$this->instance->referenceNumber());
		$data = array('reservation_id'=>$this->series->seriesId(),
						'start_date'=> $this->instance->startDate()->toDatabase(),
						'end_date'=>$this->instance->endDate()->toDatabase()
					);
		$table->load($keys);
		$table->bind($data);
		$table->check();
		return $table->store();
		/*
		foreach ($this->instance->removedParticipants() as $participantId)
		{
			//$removeReservationUser = new RemoveReservationUserCommand($instanceId, $participantId);
	
			//$database->Execute($removeReservationUser);
		}
	
		foreach ($this->instance->removedInvitees() as $inviteeId)
		{
			$insertReservationUser = new RemoveReservationUserCommand($instanceId, $inviteeId);
	
			$database->Execute($insertReservationUser);
		}
	
		foreach ($this->instance->AddedParticipants() as $participantId)
		{
			$insertReservationUser = new AddReservationUserCommand($instanceId, $participantId, ReservationUserLevel::PARTICIPANT);
	
			$database->Execute($insertReservationUser);
		}
	
		foreach ($this->instance->AddedInvitees() as $inviteeId)
		{
			$insertReservationUser = new AddReservationUserCommand($instanceId, $inviteeId, ReservationUserLevel::INVITEE);
	
			$database->Execute($insertReservationUser);
		}
		*/
	}	
}