<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;


interface IScheduleReservationList
{
	/**
	 * @return array[int]IReservationSlot
	 */
	function buildSlots();
}

class RFScheduleReservationList implements IScheduleReservationList
{
	/**
	 * @var array|ReservationListItem[]
	 */
	private $_items;

	/**
	 * @var IScheduleLayout
	 */
	private $_layout;

	/**
	 * @var Date
	 */
	private $_layoutDateStart;

	/**
	 * @var Date
	 */
	private $_layoutDateEnd;

	/**
	 * @var array|SchedulePeriod[]
	 */
	private $_layoutItems;

	private $_itemsByStartTime = array();

	/**
	 * @var array|SchedulePeriod[]
	 */
	private $_layoutByStartTime = array();

	/**
	 * @var array|int[]
	 */
	private $_layoutIndexByEndTime = array();

	/**
	 * @var Time
	 */
	private $_midnight;

	/**
	 * @var string
	 */
	private $_destinationTimezone;

	/**
	 * @var Date
	 */
	private $_firstLayoutTime;

	/**
	 * @param array|ReservationListItem[] $items
	 * @param IScheduleLayout $layout
	 * @param Date $layoutDate
	 * @param bool $hideBlockedPeriods
	 */
	public function __construct($items, ILayoutSchedule $layout, RFDate $layoutDate, $hideBlockedPeriods = false)
	{
		$this->_items = $items;
		$this->_layout = $layout;
		$this->_destinationTimezone = $this->_layout->timezone();
		$this->_layoutDateStart = $layoutDate->toTimezone($this->_destinationTimezone)->getDate();
		$this->_layoutDateEnd = $this->_layoutDateStart->addDays(1);
		$this->_layoutItems = $this->_layout->getLayout($layoutDate, $hideBlockedPeriods); //daily layout
		$this->_midnight = new RFTime(0, 0, 0, $this->_destinationTimezone);

		$this->indexLayout();
		$this->indexItems();
	}

	public function buildSlots()
	{
		$slots = array();

		for ($currentIndex = 0; $currentIndex < count($this->_layoutItems); $currentIndex++)
		{
			$layoutItem = $this->_layoutItems[$currentIndex];
			$item = $this->getItemStartingAt($layoutItem->beginDate());

			if ($item != null)
			{
				if ($this->itemEndsOnFutureDate($item))
				{
					$endTime = $this->_layoutDateEnd;
				}
				else
				{
					$endTime = $item->endDate()->toTimezone($this->_destinationTimezone);
				}

				$endingPeriodIndex = max($this->getLayoutIndexEndingAt($endTime), $currentIndex);

				$span = ($endingPeriodIndex - $currentIndex) + 1;

				$slots[] = $item->buildSlot($layoutItem, $this->_layoutItems[$endingPeriodIndex],
											$this->_layoutDateStart, $span);

				$currentIndex = $endingPeriodIndex;
			}
			else
			{
				$slots[] = new RFReservationSlotEmpty($layoutItem, $layoutItem, $this->_layoutDateStart, $layoutItem->isReservable());
			}
		}

		return $slots;
	}

	private function indexItems()
	{
		foreach ($this->_items as $item)
		{
			if ($item->endDate()->toTimezone($this->_destinationTimezone)->equals($this->_firstLayoutTime))
			{
				continue;
			}

			$start = $item->startDate()->toTimezone($this->_destinationTimezone);

			$startsInPast = $this->itemStartsOnPastDate($item);
			if ($startsInPast)
			{
				$start = $this->_firstLayoutTime;
			}
			elseif ($this->itemIsNotOnLayoutBoundary($item))
			{
				$layoutItem = $this->findClosestLayoutIndexBeforeStartingTime($item);
				if (!empty($layoutItem))
				{
					$start = $layoutItem->beginDate()->toTimezone($this->_destinationTimezone);
				}
			}

			$this->_itemsByStartTime[$start->timestamp()] = $item;
		}
	}

	private function itemStartsOnPastDate(RFReservationListItem $item)
	{
		//Log::Debug("PAST");
		return $item->startDate()->lessThan($this->_layoutDateStart);
	}

	private function itemEndsOnFutureDate(RFReservationListItem $item)
	{
		//Log::Debug("%s %s %s", $reservation->GetReferenceNumber(), $reservation->GetEndDate()->GetDate(), $this->_layoutDateEnd->GetDate());
		return $item->endDate()->compare($this->_layoutDateEnd) >= 0;
	}

	private function indexLayout()
	{
		if (!LayoutIndexCache::contains($this->_layoutDateStart))
		{
			LayoutIndexCache::add($this->_layoutDateStart, $this->_layoutItems, $this->_layoutDateStart,
								  $this->_layoutDateEnd);
		}
		$cachedIndex = LayoutIndexCache::get($this->_layoutDateStart);
		$this->_firstLayoutTime = $cachedIndex->getFirstLayoutTime();
		$this->_layoutByStartTime = $cachedIndex->layoutByStartTime();
		$this->_layoutIndexByEndTime = $cachedIndex->layoutIndexByEndTime();
	}

	/**
	 * @param Date $endingTime
	 * @return int index of $_layoutItems which has the corresponding $endingTime
	 */
	private function getLayoutIndexEndingAt(RFDate $endingTime)
	{
		$timeKey = $endingTime->timestamp();

		if (array_key_exists($timeKey, $this->_layoutIndexByEndTime))
		{
			return $this->_layoutIndexByEndTime[$timeKey];
		}

		return $this->findClosestLayoutIndexBeforeEndingTime($endingTime);
	}

	/**
	 * @param Date $beginTime
	 * @return ReservationListItem
	 */
	private function getItemStartingAt(RFDate $beginTime)
	{
		$timeKey = $beginTime->timestamp();
		if (array_key_exists($timeKey, $this->_itemsByStartTime))
		{
			return $this->_itemsByStartTime[$timeKey];
		}
		return null;
	}

	/**
	 * @param Date $endingTime
	 * @return int index of $_layoutItems which has the closest ending time to $endingTime without going past it
	 */
	private function findClosestLayoutIndexBeforeEndingTime(RFDate $endingTime)
	{
		for ($i = count($this->_layoutItems) - 1; $i >= 0; $i--)
		{
			$currentItem = $this->_layoutItems[$i];

			if ($currentItem->beginDate()->lessThan($endingTime))
			{
				return $i;
			}
		}

		return 0;
	}

	/**
	 * @param ReservationListItem $item
	 * @return SchedulePeriod which has the closest starting time to $endingTime without going prior to it
	 */
	private function findClosestLayoutIndexBeforeStartingTime(RFReservationListItem $item)
	{
		for ($i = count($this->_layoutItems) - 1; $i >= 0; $i--)
		{
			$currentItem = $this->_layoutItems[$i];

			if ($currentItem->beginDate()->lessThan($item->startDate()))
			{
				return $currentItem;
			}
		}

		Log::Error('Could not find a fitting starting slot for reservation. Id %s, ResourceId: %s, Start: %s, End: %s',
				   $item->id(), $item->resourceId(), $item->startDate()->toString(), $item->endDate()->toString());
		return null;
	}

	/**
	 * @param ReservationListItem $item
	 * @return bool
	 */
	private function itemIsNotOnLayoutBoundary(RFReservationListItem $item)
	{
		$timeKey = $item->startDate()->timestamp();
		return !(array_key_exists($timeKey, $this->_layoutByStartTime));
	}
}

class LayoutIndexCache
{
	/**
	 * @var CachedLayoutIndex[]
	 */
	private static $_cache = array();

	/**
	 * @param Date $date
	 * @return bool
	 */
	public static function contains(RFDate $date)
	{
		return array_key_exists($date->timestamp(), self::$_cache);
	}

	/**
	 * @param Date $date
	 * @param SchedulePeriod[] $schedulePeriods
	 * @param Date $startDate
	 * @param Date $endDate
	 */
	public static function add(RFDate $date, $schedulePeriods, RFDate $startDate, RFDate $endDate)
	{
		self::$_cache[$date->timestamp()] = new CachedLayoutIndex($schedulePeriods, $startDate, $endDate);
	}

	public static function get(RFDate $date)
	{
		return self::$_cache[$date->timestamp()];
	}

	public static function clear() { self::$_cache = array(); }
}

class CachedLayoutIndex
{
	private $_firstLayoutTime;
	private $_layoutByStartTime = array();
	private $_layoutIndexByEndTime = array();

	/**
	 * @param SchedulePeriod[] $schedulePeriods
	 * @param Date $startDate
	 * @param Date $endDate
	 */
	public function __construct($schedulePeriods, RFDate $startDate, RFDate $endDate)
	{
		$this->_firstLayoutTime = $endDate;

		for ($i = 0; $i < count($schedulePeriods); $i++)
		{
			/** @var Date $itemBegin */
			$itemBegin = $schedulePeriods[$i]->BeginDate();
			if ($itemBegin->LessThan($this->_firstLayoutTime))
			{
				$this->_firstLayoutTime = $schedulePeriods[$i]->BeginDate();
			}

			/** @var Date $endTime */
			$endTime = $schedulePeriods[$i]->endDate();
			if (!$schedulePeriods[$i]->endDate()->dateEquals($startDate))
			{
				$endTime = $endDate;
			}

			$this->_layoutByStartTime[$itemBegin->timestamp()] = $schedulePeriods[$i];
			$this->_layoutIndexByEndTime[$endTime->timestamp()] = $i;
		}
	}

	public function getFirstLayoutTime() { return $this->_firstLayoutTime; }

	public function layoutByStartTime() { return $this->_layoutByStartTime; }

	public function layoutIndexByEndTime() { return $this->_layoutIndexByEndTime; }
}

?>