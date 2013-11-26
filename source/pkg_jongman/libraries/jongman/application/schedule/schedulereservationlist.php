<?php
/**
Copyright 2011-2013 Nick Korbel

This file is part of phpScheduleIt.

phpScheduleIt is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

phpScheduleIt is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with phpScheduleIt.  If not, see <http://www.gnu.org/licenses/>.
 */

jimport('jongman.application.schedule.reservationslot');
jimport('jongman.application.schedule.emptyreservationslot');
interface IScheduleReservationList
{
	/**
	 * @return array[int]IReservationSlot
	 */
	function buildSlots();
}

class ScheduleReservationList implements IScheduleReservationList
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
	public function __construct($items, IScheduleLayout $layout, JMDate $layoutDate, $hideBlockedPeriods = false)
	{
		$this->_items = $items;
		$this->_layout = $layout;
		$this->_destinationTimezone = $this->_layout->timezone();
		$this->_layoutDateStart = $layoutDate->toTimezone($this->_destinationTimezone)->getDate();
		$this->_layoutDateEnd = $this->_layoutDateStart->addDays(1);
		$this->_layoutItems = $this->_layout->getLayout($layoutDate, $hideBlockedPeriods); //daily layout
		$this->_midnight = new Time(0, 0, 0, $this->_destinationTimezone);

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
				$slots[] = new EmptyReservationSlot($layoutItem, $layoutItem, $this->_layoutDateStart, $layoutItem->isReservable());
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

	private function itemStartsOnPastDate(ReservationListItem $item)
	{
		//Log::Debug("PAST");
		return $item->startDate()->lessThan($this->_layoutDateStart);
	}

	private function itemEndsOnFutureDate(ReservationListItem $item)
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
	private function getLayoutIndexEndingAt(JMDate $endingTime)
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
	private function getItemStartingAt(JMDate $beginTime)
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
	private function findClosestLayoutIndexBeforeEndingTime(JMDate $endingTime)
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
	private function findClosestLayoutIndexBeforeStartingTime(ReservationListItem $item)
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
	private function itemIsNotOnLayoutBoundary(ReservationListItem $item)
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
	public static function contains(JMDate $date)
	{
		return array_key_exists($date->timestamp(), self::$_cache);
	}

	/**
	 * @param Date $date
	 * @param SchedulePeriod[] $schedulePeriods
	 * @param Date $startDate
	 * @param Date $endDate
	 */
	public static function add(JMDate $date, $schedulePeriods, JMDate $startDate, JMDate $endDate)
	{
		self::$_cache[$date->timestamp()] = new CachedLayoutIndex($schedulePeriods, $startDate, $endDate);
	}

	public static function get(JMDate $date)
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
	public function __construct($schedulePeriods, JMDate $startDate, JMDate $endDate)
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