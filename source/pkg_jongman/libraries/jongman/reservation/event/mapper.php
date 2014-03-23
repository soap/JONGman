<?php
defined('_JEXEC') or die;


class RFReservationEventMapper
{
	private $buildMethods = array();
	private static $instance;

	private function __construct()
	{
		$this->buildMethods['SeriesDeletedEvent'] = 'buildDeleteSeriesCommand';
		$this->buildMethods['OwnerChangedEvent'] = 'ownerChangedCommand';

		$this->buildMethods['InstanceAddedEvent'] = 'buildAddReservationCommand';
		$this->buildMethods['InstanceRemovedEvent'] = 'buildRemoveReservationCommand';
		$this->buildMethods['InstanceUpdatedEvent'] = 'buildUpdateReservationCommand';

		$this->buildMethods['ResourceRemovedEvent'] = 'buildRemoveResourceCommand';
		$this->buildMethods['ResourceAddedEvent'] = 'buildAddResourceCommand';

		$this->buildMethods['AccessoryAddedEvent'] = 'buildAddAccessoryCommand';
		$this->buildMethods['AccessoryRemovedEvent'] = 'buildRemoveAccessoryCommand';

		$this->buildMethods['AttributeAddedEvent'] = 'buildAddAttributeCommand';
		$this->buildMethods['AttributeRemovedEvent'] = 'buildRemoveAttributeCommand';

		$this->buildMethods['AttachmentRemovedEvent'] = 'buildAttachmentRemovedEvent';

		$this->buildMethods['ReminderAddedEvent'] = 'buildReminderAddedEvent';
		$this->buildMethods['ReminderRemovedEvent'] = 'buildReminderRemovedEvent';
	}

	/**
	 * @static
	 * @return ReservationEventMapper
	 */
	public static function getInstance()
	{
		if (!isset(self::$instance))
		{
			self::$instance = new RFReservationEventMapper();
		}

		return self::$instance;
	}

	/**
	 * @param $event mixed
	 * @param $series ExistingReservationSeries
	 * @return RFEventCommand
	 */
	public function map($event, RFReservationExistingseries $series)
	{
		$eventType = get_class($event);
		if (!isset($this->buildMethods[$eventType]))
		{
			Log::Debug("No command event mapper found for event $eventType");
			return null;
		}

		$method = $this->buildMethods[$eventType];
		return $this->$method($event, $series);
	}

	private function buildDeleteSeriesCommand(RFEventSeriesDeleted $event)
	{
		return new RFEventCommandDeleteseries($event->series());
	}

	private function buildAddReservationCommand(RFEventInstanceAdded $event, RFReservationExistingseries $series)
	{
		return new RFEeventCommandInstanceadded($event->getInstance(), $series);
	}

	private function buildRemoveReservationCommand(InstanceRemovedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommandInstanceremoved($event->getInstance(), $series);
	}

	private function buildUpdateReservationCommand(InstanceUpdatedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommandInstanceupdated($event->getInstance(), $series);
	}

	private function ownerChangedCommand(OwnerChangedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommandOwnerchanged($event);
	}
	
	private function buildRemoveResourceCommand(ResourceRemovedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new RemoveReservationResourceCommand($series->seriesId(), $event->resourceId()), $series);
	}

	private function buildAddResourceCommand(ResourceAddedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new AddReservationResourceCommand($series->seriesId(), $event->resourceId(), $event->resourceLevel()), $series);
	}

	private function buildAddAccessoryCommand(AccessoryAddedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new AddReservationAccessoryCommand($event->AccessoryId(), $event->Quantity(), $series->SeriesId()), $series);
	}

	private function buildRemoveAccessoryCommand(AccessoryRemovedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new RemoveReservationAccessoryCommand($series->SeriesId(), $event->AccessoryId()), $series);
	}

	private function buildAddAttributeCommand(AttributeAddedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new AddAttributeValueCommand($event->AttributeId(), $event->Value(), $series->SeriesId(), CustomAttributeCategory::RESERVATION), $series);
	}

	private function buildRemoveAttributeCommand(AttributeRemovedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new RemoveAttributeValueCommand($event->AttributeId(), $series->SeriesId()), $series);
	}

	private function buildAttachmentRemovedEvent(AttachmentRemovedEvent $event, RFReservationExistingseries $series)
	{
		return new AttachmentRemovedCommand($event);
	}

	private function buildReminderAddedEvent(ReminderAddedEvent $event, RFReservationExistingseries $series)
	{
		return new ReminderAddedCommand($event);
	}

	private function buildReminderRemovedEvent(ReminderRemovedEvent $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new RemoveReservationReminderCommand($series->SeriesId(), $event->ReminderType()), $series);
	}
}