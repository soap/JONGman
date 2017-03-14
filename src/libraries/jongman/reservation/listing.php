<?php
defined('_JEXEC') or die;
jimport('jongman.base.ireservationlisting');

class RFReservationListing implements IMutableReservationListing
{
	/**
	 * @var string
	 */
	protected $timezone;

	/**
	 * @var array|ReservationItemView[]
	 */
	protected $_reservations = array();
	
	/**
	 * @var array|ReservationItemView[]
	 */
	protected $_reservationByResource = array();

	/**
	 * @var array|ReservationItemView[]
	 */
	protected $_reservationsByDate = array();

	/**
	 * @var array|ReservationItemView[]
	 */
	protected $_reservationsByDateAndResource = array();

	/**
	 * @param string $targetTimezone
	 */
	public function __construct($targetTimezone)
	{
		$this->timezone = $targetTimezone;
	}
	
	public function add($reservation)
	{
		$this->addItem(new RFReservationListItem($reservation));
	}

	public function addBlackout($blackout)
	{
		$this->addItem(new RFBlackoutListItem($blackout));
	}

	protected function addItem(RFReservationListItem $item)
	{
		$currentDate = $item->startDate()->toTimezone($this->timezone);
		$lastDate = $item->endDate()->toTimezone($this->timezone);

		if ($currentDate->dateEquals($lastDate))
		{
			$this->addOnDate($item, $currentDate);
		}
		else
		{
			while (!$currentDate->dateEquals($lastDate))
			{
				$this->addOnDate($item, $currentDate);
				$currentDate = $currentDate->addDays(1);
			}
			$this->addOnDate($item, $lastDate);
		}

		$this->_reservations[] = $item;
		$this->_reservationByResource[$item->resourceId()][] = $item;
	}

	protected function addOnDate(RFReservationListItem $item, RFDate $date)
	{
//		Log::Debug('Adding id %s on %s', $item->Id(), $date);
		$this->_reservationsByDate[$date->format('Ymd')][] = $item;
		$this->_reservationsByDateAndResource[$date->format('Ymd') . '|' . $item->resourceId()][] = $item;
	}
	
	public function count()
	{
		return count($this->_reservations);
	}
	
	public function reservations()
	{
		return $this->_reservations;
	}

	/**
	 * @param array|ReservationListItem[] $reservations
	 * @return ReservationListing
	 */
	private function create($reservations)
	{
		$reservationListing = new RFReservationListing($this->timezone);

		if ($reservations != null)
		{
			foreach($reservations as $reservation)
			{
				$reservationListing->addItem($reservation);
			}
		}

		return $reservationListing;
	}

	/**
	 * @param Date $date
	 * @return ReservationListing
	 */
	public function onDate($date)
	{
//		Log::Debug('Found %s reservations on %s', count($this->_reservationsByDate[$date->Format('Ymd')]), $date);

        $key = $date->Format('Ymd');
        $reservations = array();
        if (array_key_exists($key, $this->_reservationsByDate))
        {
            $reservations = $this->_reservationsByDate[$key];
        }
        return $this->create($reservations);
	}
	
	public function forResource($resourceId)
	{
		if (array_key_exists($resourceId, $this->_reservationByResource))
		{
			return $this->create($this->_reservationByResource[$resourceId]);
		}
		
		return new RFReservationListing($this->timezone);
	}

	public function onDateForResource(RFDate $date, $resourceId)
	{
        $key = $date->format('Ymd') . '|' . $resourceId;

		if (!array_key_exists($key,  $this->_reservationsByDateAndResource))
		{
			return array();
		}

		return $this->_reservationsByDateAndResource[$key];
	}
}
