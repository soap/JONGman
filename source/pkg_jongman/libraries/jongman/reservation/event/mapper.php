<?php
defined('_JEXEC') or die;


class RFReservationEventMapper
{
	private $buildMethods = array();
	private static $instance;

	private function __construct()
	{
		$this->buildMethods['RFEventSeriesDeleted'] = 'buildDeleteSeriesCommand';
		$this->buildMethods['RFEventOwnerChanged'] = 'ownerChangedCommand';

		$this->buildMethods['RFEventInstanceAdded'] = 'buildAddReservationCommand';
		$this->buildMethods['RFEventInstanceRemoved'] = 'buildRemoveReservationCommand';
		$this->buildMethods['RFEventInstanceUpdated'] = 'buildUpdateReservationCommand';

		$this->buildMethods['RFEventResourceRemoved'] = 'buildRemoveResourceCommand';
		$this->buildMethods['RFEventResourceAdded'] = 'buildAddResourceCommand';

		$this->buildMethods['RFEventAccessoryAdded'] = 'buildAddAccessoryCommand';
		$this->buildMethods['RFEventAccessoryRemoved'] = 'buildRemoveAccessoryCommand';

		$this->buildMethods['RFEventAttributeAdded'] = 'buildAddAttributeCommand';
		$this->buildMethods['RFEventAttributeRemoved'] = 'buildRemoveAttributeCommand';

		$this->buildMethods['RFEventAttachmentRemoved'] = 'buildAttachmentRemovedEvent';

		$this->buildMethods['RFEventReminderAdded'] = 'buildReminderAddedEvent';
		$this->buildMethods['RFEventReminderRemoved'] = 'buildReminderRemovedEvent';
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
			JFactory::getApplication()->enqueueMessage("No command event mapper found for event $eventType");
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

	private function buildRemoveReservationCommand(RFEventInstanceRemoved $event, RFReservationExistingseries $series)
	{
		return new RFEventCommandInstanceremoved($event->getInstance(), $series);
	}

	private function buildUpdateReservationCommand(RFEventInstanceUpdated $event, RFReservationExistingseries $series)
	{
		return new RFEventCommandInstanceupdated($event->getInstance(), $series);
	}

	private function ownerChangedCommand(RFEventOwnerChanged $event, RFReservationExistingseries $series)
	{
		return new RFEventCommandOwnerchanged($event);
	}
	
	private function buildRemoveResourceCommand(RFEventResourceRemoved $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new RemoveReservationResourceCommand($series->seriesId(), $event->resourceId()), $series);
	}

	private function buildAddResourceCommand(RFEventResourceAdded $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new AddReservationResourceCommand($series->seriesId(), $event->resourceId(), $event->resourceLevel()), $series);
	}

	private function buildAddAccessoryCommand(RFEventAccessoryAdded $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new AddReservationAccessoryCommand($event->AccessoryId(), $event->Quantity(), $series->SeriesId()), $series);
	}

	private function buildRemoveAccessoryCommand(RFEventAccessoryRemoved $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new RemoveReservationAccessoryCommand($series->SeriesId(), $event->AccessoryId()), $series);
	}

	private function buildAddAttributeCommand(RFEventAttributeAdded $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new AddAttributeValueCommand($event->AttributeId(), $event->Value(), $series->SeriesId(), CustomAttributeCategory::RESERVATION), $series);
	}

	private function buildRemoveAttributeCommand(RFEventAttributeRemoved $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new RemoveAttributeValueCommand($event->AttributeId(), $series->SeriesId()), $series);
	}

	private function buildAttachmentRemovedEvent(RFEventAttachmentRemoved $event, RFReservationExistingseries $series)
	{
		return new RFAttachmentRemovedCommand($event);
	}

	private function buildReminderAddedEvent(RFEventReminderAdded $event, RFReservationExistingseries $series)
	{
		return new RFReminderAddedCommand($event);
	}

	private function buildReminderRemovedEvent(RFEventReminderRemoved $event, RFReservationExistingseries $series)
	{
		return new RFEventCommand(new RemoveReservationReminderCommand($series->SeriesId(), $event->ReminderType()), $series);
	}
}