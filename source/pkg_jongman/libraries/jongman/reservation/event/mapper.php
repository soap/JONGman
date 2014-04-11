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
	public function map($event, RFReservationExistingSeries $series)
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

	private function buildAddReservationCommand(RFEventInstanceAdded $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommandInstanceAdded($event->getInstance(), $series);
	}

	private function buildRemoveReservationCommand(RFEventInstanceRemoved $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommandInstanceRemoved($event->getInstance(), $series);
	}

	private function buildUpdateReservationCommand(RFEventInstanceUpdated $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommandInstanceUpdated($event->getInstance(), $series);
	}

	private function ownerChangedCommand(RFEventOwnerChanged $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommandOwnerChanged($event);
	}
	/**
	 * 
	 * @todo rewrite it
	 * @param RFEventResourceRemoved $event
	 * @param RFReservationExistingSeries $series
	 */
	private function buildRemoveResourceCommand(RFEventResourceRemoved $event, RFReservationExistingSeries $series)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->delete('#__jongman_reservation_resources')
			->where('reservation_id = '.$series->seriesId())
			->where('resource_id = '.$event->resourceId());
		return new RFEventCommand($query, $series);
	}

	private function buildAddResourceCommand(RFEventResourceAdded $event, RFReservationExistingSeries $series)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->insert('#__jongman_reservation_resources')
			->set('reservation_id = '.$series->seriesId())
			->set('resource_id = '.$event->resourceId())
			->set('resource_level = 1');
		return new RFEventCommand($query, $series);
	}

	private function buildAddAccessoryCommand(RFEventAccessoryAdded $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommand(new AddReservationAccessoryCommand($event->AccessoryId(), $event->Quantity(), $series->SeriesId()), $series);
	}

	private function buildRemoveAccessoryCommand(RFEventAccessoryRemoved $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommand(new RemoveReservationAccessoryCommand($series->SeriesId(), $event->AccessoryId()), $series);
	}

	private function buildAddAttributeCommand(RFEventAttributeAdded $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommand(new AddAttributeValueCommand($event->AttributeId(), $event->Value(), $series->SeriesId(), CustomAttributeCategory::RESERVATION), $series);
	}

	private function buildRemoveAttributeCommand(RFEventAttributeRemoved $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommand(new RemoveAttributeValueCommand($event->AttributeId(), $series->SeriesId()), $series);
	}

	private function buildAttachmentRemovedEvent(RFEventAttachmentRemoved $event, RFReservationExistingSeries $series)
	{
		return new RFAttachmentRemovedCommand($event);
	}

	private function buildReminderAddedEvent(RFEventReminderAdded $event, RFReservationExistingSeries $series)
	{
		return new RFReminderAddedCommand($event);
	}

	private function buildReminderRemovedEvent(RFEventReminderRemoved $event, RFReservationExistingSeries $series)
	{
		return new RFEventCommand(new RemoveReservationReminderCommand($series->SeriesId(), $event->ReminderType()), $series);
	}
}