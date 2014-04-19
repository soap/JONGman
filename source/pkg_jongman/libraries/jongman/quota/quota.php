<?php
defined('_JEXEC') or die;

class RFQuota implements IQuota
{
	/**
	 * @var int
	 */
	private $quotaId;

	/**
	 * @var \IQuotaDuration
	 */
	private $duration;

	/**
	 * @var \IQuotaLimit
	 */
	private $limit;

	/**
	 * @var int
	 */
	private $resourceId;

	/**
	 * @var int
	 */
	private $groupId;

	/**
	 * @var int
	 */
	private $scheduleId;

	/**
	 * @param int $quotaId
	 * @param IQuotaDuration $duration
	 * @param IQuotaLimit $limit
	 * @param int $resourceId
	 * @param int $groupId
	 * @param int $scheduleId
	 */
	public function __construct($quotaId, $duration, $limit, $resourceId = null, $groupId = null, $scheduleId = null)
	{
		$this->quotaId = $quotaId;
		$this->duration = $duration;
		$this->limit = $limit;
		$this->resourceId = empty($resourceId) ? null : $resourceId;
		$this->groupId = empty($groupId) ? null : $groupId;
		$this->scheduleId = empty($scheduleId) ? null : $scheduleId;
	}

	/**
	 * @static
	 * @param string $duration
	 * @param decimal $limit
	 * @param string $unit
	 * @param int $resourceId
	 * @param int $groupId
	 * @param int $scheduleId
	 * @return Quota
	 */
	public static function create($duration, $limit, $unit, $resourceId, $groupId, $scheduleId)
	{
		return new RFQuota(0, self::createDuration($duration), self::createLimit($limit, $unit), $resourceId, $groupId, $scheduleId);
	}

	/**
	 * @static
	 * @param decimal $limit
	 * @param string $unit QuotaUnit
	 * @return IQuotaLimit
	 */
	public static function createLimit($limit, $unit)
	{
		if ($unit == RFQuotaUnit::Reservations)
		{
			return new RFQuotaLimitCount($limit);
		}

		return new RFQuotaLimitHours($limit);
	}

	/**
	 * @static
	 * @param string $duration QuotaDuration
	 * @return IQuotaDuration
	 */
	public static function createDuration($duration)
	{
		if ($duration == RFQuotaDuration::Day)
		{
			return new RFQuotaDurationDay();
		}

		if ($duration == RFQuotaDuration::Week)
		{
			return new RFQuotaDurationWeek();
		}

		return new RFQuotaDurationMonth();
	}

	/**
	 * @param RFReservationSeries $reservationSeries
	 * @param JUser $user
	 * @param unknown $schedule
	 * @param IReservationViewRepository $reservationViewRepository
	 * @return bool
	 */
	public function exceedsQuota($reservationSeries, $user, $schedule, IReservationRepository $reservationRepository)
	{
		$timezone = $schedule->timezone; //getTimezone();

		if (!is_null($this->resourceId))
		{
			$appliesToResource = false;

			foreach ($reservationSeries->allResourceIds() as $resourceId)
			{
				if (!$appliesToResource && $this->appliesToResource($resourceId))
				{
					$appliesToResource = true;
				}
			}

			if (!$appliesToResource)
			{
				return false;
			}
		}

		if (!is_null($this->groupId))
		{
			$appliesToGroup = false;
			if ($user instanceof JUser) {
				//array of authorised group id
				$groups = $user->getAuthorisedGroups();
			}
			foreach ($groups() as $groupId)
			{
				if (!$appliesToGroup && $this->appliesToGroup($groupId))
				{
					$appliesToGroup = true;
				}
			}

			if (!$appliesToGroup)
			{
				return false;
			}
		}

		if (!$this->appliesToSchedule($reservationSeries->scheduleId()))
		{
			return false;
		}

		if (count($reservationSeries->instances()) == 0)
		{
			return false;
		}

		$dates = $this->duration->getSearchDates($reservationSeries, $timezone);
		$reservationsWithinRange = $reservationRepository->getReservationList($dates->start(), $dates->end(), $reservationSeries->userId(), 1 /*OWNER*/);

		try
		{
			$this->checkAll($reservationsWithinRange, $reservationSeries, $timezone);
		}
		catch (RFQuotaExceededException $ex)
		{
			return true;
		}

		return false;
	}

	public function __toString()
	{
		return $this->quotaId . '';
	}

	/**
	 * @return IQuotaLimit
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * @return IQuotaDuration
	 */
	public function getDuration()
	{
		return $this->duration;
	}

	/**
	 * @param int $resourceId
	 * @return bool
	 */
	public function appliesToResource($resourceId)
	{
		return is_null($this->resourceId) || $this->resourceId == $resourceId;
	}

	/**
	 * @param int $groupId
	 * @return bool
	 */
	public function appliesToGroup($groupId)
	{
		return is_null($this->groupId) || $this->groupId == $groupId;
	}

	/**
	 * @param int $scheduleId
	 * @return bool
	 */
	public function appliesToSchedule($scheduleId)
	{
		return is_null($this->scheduleId) || $this->scheduleId == $scheduleId;
	}

	/**
	 * @return int|null
	 */
	public function resourceId()
	{
		return $this->resourceId;
	}

	/**
	 * @return int|null
	 */
	public function groupId()
	{
		return $this->groupId;
	}

	/**
	 * @return int|null
	 */
	public function scheduleId()
	{
		return $this->scheduleId;
	}

	private function addExisting(RFReservationItem/*View*/ $reservation, $timezone)
	{
		$this->_breakAndAdd($reservation->startDate, $reservation->endDate, $timezone);
	}

	private function addInstance(RFReservation $reservation, $timezone)
	{
		$this->_breakAndAdd($reservation->startDate(), $reservation->endDate(), $timezone);
	}

	/**
	 * @param array|ReservationItemView[] $reservationsWithinRange
	 * @param ReservationSeries $series
	 * @param string $timezone
	 * @throws QuotaExceededException
	 */
	private function checkAll($reservationsWithinRange, $series, $timezone)
	{
		$toBeSkipped = array();

		/** @var $instance Reservation */
		foreach ($series->getInstances() as $instance)
		{
			$toBeSkipped[$instance->referenceNumber()] = true;

			if (!is_null($this->scheduleId))
			{
				foreach ($series->allResources() as $resource)
				{
					// add each resource instance
					if ($this->appliesToResource($resource->getResourceId()))
					{
						$this->addInstance($instance, $timezone);
					}
				}
			}
			else
			{
				$this->addInstance($instance, $timezone);
			}
		}

		/** @var $reservation ReservationItemView */
		foreach ($reservationsWithinRange as $reservation)
		{
			if (($series->containsResource($reservation->ResourceId) || $series->ScheduleId() == $reservation->ScheduleId) &&
					!array_key_exists($reservation->referenceNumber, $toBeSkipped) &&
					!$this->willBeDeleted($series, $reservation->reservationId)
			)
			{
				$this->AddExisting($reservation, $timezone);
			}
		}
	}

	/**
	 * @param ExistingReservationSeries $series
	 * @param int $reservationId
	 * @return bool
	 */
	private function willBeDeleted($series, $reservationId)
	{
		if (method_exists($series, 'isMarkedForDelete'))
		{
			return $series->isMarkedForDelete($reservationId);
		}

		return false;
	}

	private function _breakAndAdd(RFDate $startDate, RFDate $endDate, $timezone)
	{
		$start = $startDate->toTimezone($timezone);
		$end = $endDate->toTimezone($timezone);

		$range = new RFDateRange($start, $end);

		$ranges = $this->duration->split($range);

		foreach ($ranges as $dr)
		{
			$this->_add($dr);
		}
	}

	private function _add(RFDateRange $dateRange)
	{
		$durationKey = $this->duration->getDurationKey($dateRange->getBegin());

		$this->limit->tryAdd($dateRange->getBegin(), $dateRange->getEnd(), $durationKey);
	}

}

