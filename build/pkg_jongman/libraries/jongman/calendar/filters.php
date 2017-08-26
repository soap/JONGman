<?php
defined('_JEXEC') or die;

class RFCalendarFilters
{
	const FilterSchedule = 'schedule';
	const FilterResource = 'resource';

	/**
	 * @var array|CalendarFilter[]
	 */
	private $filters = array();

	/**
	 * @var ResourceGroupTree
	*/
	private $resourceGroupTree;

	/**
	 * @param array|RFSchedule[] $schedules
	 * @param array|RFResourceDto[] $resources
	 * @param int $selectedScheduleId
	 * @param int $selectedResourceId
	 * @param RFResourceGroupTree $resourceGroupTree
	 */
	public function __construct($schedules, $resources, $selectedScheduleId, $selectedResourceId, RFResourceGroupTree $resourceGroupTree=null)
	{
		$this->resourceGroupTree = $resourceGroupTree;

		if (!empty($resources))
		{
			$this->filters[] = new RFCalendarFilter(self::FilterSchedule, null, JText::_('COM_JONGMAN_ALL_RESERVATIONS'), (empty($selectedResourceId) && empty($selectedScheduleId)));
		}
		foreach ($schedules as $schedule)
		{
			if ($this->scheduleContainsNoResources($schedule, $resources))
			{
				continue;
			}

			$filter = new RFCalendarFilter(self::FilterSchedule, $schedule->getId(), $schedule->getName(), (empty($selectedResourceId) && $selectedScheduleId == $schedule->getId()));

			foreach ($resources as $resource)
			{
				if ($resource->getScheduleId() == $schedule->getId())
				{
					$filter->addSubFilter(new RFCalendarFilter(self::FilterResource, $resource->getResourceId(), $resource->getName(), ($selectedResourceId == $resource->getResourceId())));
				}
			}

			$this->filters[] = $filter;
		}
	}

	/**
	 * @return bool
	 */
	public function isEmpty()
	{
		return empty($this->filters);
	}

	/**
	 * @return array|RFCalendarFilter[]
	 */
	public function getFilters()
	{
		return $this->filters;
	}

	/**
	 * @return ResourceGroupTree
	 */
	public function getResourceGroupTree()
	{
		return $this->resourceGroupTree;
	}

	/**
	 * @param Schedule $schedule
	 * @param ResourceDto[] $resources
	 * @return bool
	 */
	private function scheduleContainsNoResources(RFSchedule $schedule, $resources)
	{
		foreach ($resources as $resource)
		{
			if ($resource->getScheduleId() == $schedule->getId())
			{
				return false;
			}
		}

		return true;
	}
}