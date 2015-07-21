<?php
defined('_JEXEC') or die;

class RFBlackoutSeries
{
	/**
	 * @var int
	 */
	protected $seriesId;

	/**
	 * @var int
	 */
	protected $ownerId;

	/**
	 * @var int[]
	 */
	protected $resourceIds = array();

	/**
	 * @var BlackoutResource[]
	*/
	protected $resources = array();

	/**
	 * @var Blackout[]
	*/
	protected $blackouts= array();

	/**
	 * @var string
	*/
	protected $title;

	/**
	 * @var DateRange
	 */
	protected $blackoutDate;

	/**
	 * @var IRepeatOptions
	 */
	protected $repeatOptions;

	/**
	 * @var RepeatConfiguration
	 */
	protected $repeatConfiguration;

	/**
	 * @var bool
	 */
	protected $isNew = true;

	protected $currentBlackoutInstanceId;

	/**
	 * @param int $userId
	 * @param string $title
	 */
	public function __construct($userId, $title)
	{
		$this->withRepeatOptions(new RFReservationRepeatNone());
		$this->ownerId = $userId;
		$this->title = $title;
	}

	/**
	 * @param $userId
	 * @param $title
	 * @param RFDateRange $blackoutDate
	 * @return RFBlackoutSeries
	 */
	public static function create($userId, $title, RFDateRange $blackoutDate)
	{
		$series = new RFBlackoutSeries($userId, $title);
		$series->addBlackout(new RFBlackout($blackoutDate));
		$series->setCurrentBlackout($blackoutDate);
		return $series;
	}

	/**
	 * @param int $ownerId
	 * @param SeriesUpdateScope|string $scope
	 * @param string $title
	 * @param RFDateRange $blackoutDate
	 * @param IRepeatOptions $repeatOptions
	 * @param int[] $resourceIds
	 */
	public function update($ownerId, $scope, $title, $blackoutDate, $repeatOptions, $resourceIds)
	{
		$this->ownerId = $ownerId;
		$this->title = $title;
		$this->resourceIds = array();
		foreach($resourceIds as $rid)
		{
			$this->addResourceId($rid);
		}

		if ($scope == RFSeriesUpdateScope::ThisInstance)
		{
			$this->blackouts = array();
			$this->addBlackout(new RFBlackout($blackoutDate));
			$this->setCurrentBlackout($blackoutDate);

			$this->repeats(new RFReservationRepeatNone());
		}
		else
		{
			$currentDate = $this->currentBlackout()->date();
			$newDate = $blackoutDate;

			$startDiff = RFDateDiff::betweenDates($currentDate->getBegin(), $newDate->getBegin());
			$endDiff = RFDateDiff::betweenDates($currentDate->getEnd(), $newDate->getEnd());

			$earliestDate = $this->getEarliestDate($blackoutDate);

			if (!$earliestDate->equals($blackoutDate))
			{
				$earliestDate = new RFDateRange($earliestDate->getBegin()->applyDifference($startDiff), $earliestDate->getEnd()->applyDifference($endDiff));
			}

			$this->blackouts = array();

			$this->addBlackout(new RFBlackout($earliestDate));
			$this->setCurrentBlackout($earliestDate);
			$this->repeats($repeatOptions);
		}

		$this->isNew = $scope == RFSeriesUpdateScope::ThisInstance;
	}

	private function getEarliestDate(RFDateRange $blackoutDate)
	{
		$earliestDate = $blackoutDate;

		foreach($this->blackouts as $blackout)
		{
			if ($blackout->startDate()->lessThan($earliestDate->getBegin()))
			{
				$earliestDate = $blackout->date();
			}

		}

		return $earliestDate;
	}

	/**
	 * @return bool
	 */
	public function isNew()
	{
		return $this->isNew;
	}

	/**
	 * @return int[]
	 */
	public function resourceIds()
	{
		return $this->resourceIds;
	}

	/**
	 * @return int
	 */
	public function ownerId()
	{
		return $this->ownerId;
	}

	/**
	 * @return string
	 */
	public function title()
	{
		return $this->title;
	}

	/**
	 * @param $resourceId int
	 */
	public function addResourceId($resourceId)
	{
		$this->resourceIds[] = $resourceId;
	}

	public function addResource(RFBlackoutResource $resource)
	{
		$this->addResourceId($resource->getId());
		$this->resources[] = $resource;
	}

	public function addBlackout(RFBlackout $blackout)
	{
		$this->blackouts[$this->toKey($blackout->date())] = $blackout;
	}

	/**
	 * @return RFBlackout[]
	 */
	public function allBlackouts()
	{
		asort($this->blackouts);
		return array_values($this->blackouts);
	}

	/**
	 * @return int
	 */
	public function resourceId()
	{
		return $this->resourceIds[0];
	}

	/**
	 * @param int $resourceId
	 * @return bool
	 */
	public function containsResource($resourceId)
	{
		return in_array($resourceId, $this->resourceIds);
	}

	/**
	 * @param IRepeatOptions $repeatOptions
	 */
	public function repeats(IRepeatOptions $repeatOptions)
	{
		$this->withRepeatOptions($repeatOptions);
		foreach ($repeatOptions->getDates($this->blackoutDate) as $date)
		{
			$this->addBlackout(new RFBlackout($date));
		}
	}

	/**
	 * @return string
	 */
	public function repeatType()
	{
		return $this->repeatOptions->repeatType();
	}

	/**
	 * @return string
	 */
	public function repeatConfigurationString()
	{
		return $this->repeatOptions->configurationString();
	}

	public function repeatConfiguration()
	{
		return $this->repeatConfiguration;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->seriesId;
	}
	
	public function id()
	{
		return $this->seriesId;
	}

	public function withId($id)
	{
		$this->seriesId = $id;
	}

	public function withRepeatOptions(IRepeatOptions $repeatOptions)
	{
		$this->repeatOptions = $repeatOptions;
		$this->repeatConfiguration = RFRepeatConfiguration::create($repeatOptions->repeatType(), $repeatOptions->configurationString());
	}

	public function setCurrentBlackout(RFDateRange $date)
	{
		$this->blackoutDate = $date;
	}

	protected function withCurrentBlackoutId($blackoutInstanceId)
	{
		$this->currentBlackoutInstanceId = $blackoutInstanceId;
	}


	/**
	 * @param string[] $row
	 * @return RFBlackoutSeries
	 */
	public static function fromRow($row)
	{
		$series = new RFBlackoutSeries($row->created_by, $row->title);
		$series->withId($row->id);
		$series->setCurrentBlackout(new RFDateRange(RFDate::fromDatabase($row->start_date), RFDate::fromDatabase($row->end_date)));
		$series->withCurrentBlackoutId($row->instance_id);
		$configuration = RFRepeatConfiguration::create($row->repeat_type, $row->repeat_options);
		$factory = new RFReservationRepeatOptionsFactory();
		$options = $factory->create($row->repeat_type, $configuration->interval, $configuration->terminationDate,
				$configuration->weekdays, $configuration->monthlyType);

		$series->withRepeatOptions($options);

		return $series;
	}

	/**
	 * @return RFBlackout
	 */
	public function currentBlackout()
	{
		return $this->blackouts[$this->toKey($this->blackoutDate)];
	}

	/**
	 * @param RFDateRange $date
	 * @return string
	 */
	private function toKey(RFDateRange $date)
	{
		return $date->getBegin()->timestamp();
	}

	/**
	 * @return RFBlackoutResource[]
	 */
	public function resources()
	{
		return $this->resources;
	}

	/**
	 * @return int
	 */
	public function currentBlackoutInstanceId()
	{
		return $this->currentBlackoutInstanceId;
	}
}