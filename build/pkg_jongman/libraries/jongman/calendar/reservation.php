<?php
defined('_JEXEC') or die;

class RFCalendarReservation
{
	/**
	 * 
	 * @var int
	 */
	public $seriesId;
	
	/**
	 * @var RFDate
	 */
	public $startDate;

	/**
	 * @var RFDate
	 */
	public $endDate;

	/**
	 * @var string
	 */
	public $resourceName;

	/**
	 * @var string
	 */
	public $referenceNumber;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $description;

	/**
	 * @var bool
	 */
	public $invited;

	/**
	 * @var bool
	 */
	public $participant;

	/**
	 * @var bool
	 */
	public $owner;

	/**
	 * @var string
	 */
	public $ownerName;

	/**
	 * @var string
	 */
	public $owwnerFirst;

	/**
	 * @var string
	 */
	public $ownerLast;

	/**
	 * @var string
	 */
	public $displayTitle;

	/**
	 * @var string
	 */
	public $color;

	/**
	 * @var string
	 */
	public $textColor;

	/**
	 * @var string
	 */
	public $class;

	private function __construct(RFDate $startDate, RFDate $endDate, $resourceName, $referenceNumber)
	{
		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->resourceName = $resourceName;
		$this->referenceNumber = $referenceNumber;
	}

	/**
	 * @param $reservations array|ReservationItemView[]
	 * @param $timezone string
	 * @param $user UserSession
	 * @param $groupSeriesByResource bool
	 * @return array|RFCalendarReservation[]
	 */
	public static function fromViewList($reservations, $timezone, $user, $groupSeriesByResource = false)
	{
		$knownSeries = array();
		$results = array();

		foreach ($reservations as $reservation)
		{
			if ($groupSeriesByResource)
			{
				if (array_key_exists($reservation->reservationId, $knownSeries))
				{
					continue;
				}
				$knownSeries[$reservation->reservationId] = true;
			}
			$results[] = self::fromView($reservation, $timezone, $user);
		}
		return $results;
	}

	/**
	 * @param $reservation ReservationItemView
	 * @param $timezone string
	 * @param $user UserSession
	 * @return RFCalendarReservation
	 */
	public static function fromView($reservation, $timezone, $user)
	{
		$factory = new RFSlotLabelFactory($user);
		$start = $reservation->startDate->toTimezone($timezone);
		$end = $reservation->endDate->ToTimezone($timezone);
		$resourceName = $reservation->resourceName;
		$referenceNumber = $reservation->referenceNumber;

		$res = new RFCalendarReservation($start, $end, $resourceName, $referenceNumber);

		$res->seriesId = $reservation->reservationId;
		$res->title = $reservation->title;
		$res->description = $reservation->description;
		$res->displayTitle = $factory->Format($reservation, Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION_LABELS,
				ConfigKeys::RESERVATION_LABELS_MY_CALENDAR));
		$res->invited = $reservation->userLevelId == RFReservationUserLevel::INVITEE;
		$res->participant = $reservation->UserLevelId == RFReservationUserLevel::PARTICIPANT;
		$res->owner = $reservation->UserLevelId == RFReservationUserLevel::OWNER;

		$color = RFApplicationHelper::getReservationColor($res->seriesId);
		if (!empty($color))
		{
			$res->color = "#$color";
			$res->textColor = new RFContrastingColor($color);
		}

		$res->class = self::getClass($reservation);

		return $res;
	}

	/**
	 * @static
	 * @param array|RFReservationItemView[] $reservations
	 * @param array|ResourceDto[] $resources
	 * @param JUser $user
	 * @param bool $groupSeriesByResource
	 * @return array|RFCalendarReservation[]
	 */
	public static function fromScheduleReservationList($reservations, $resources, JUser $user, $groupSeriesByResource = false)
	{
		$knownSeries = array();
		$factory = new RFSlotLabelFactory($user);

		$resourceMap = array();
		/** @var $resource ResourceDto */
		foreach ($resources as $resource)
		{
			$resourceMap[$resource->getResourceId()] = $resource->getName();
		}
		
		$res = array();
		foreach ($reservations as $reservation)
		{
			
			if (!array_key_exists($reservation->resourceId, $resourceMap))
			{
				
				continue;
			}

			if ($groupSeriesByResource)
			{
				if (array_key_exists($reservation->reservationId, $knownSeries))
				{
					
					continue;
				}
				$knownSeries[$reservation->reservationId] = true;
			}

			$timezone = RFApplicationHelper::getUserTimezone($user->id);
			$start = $reservation->startDate->toTimezone($timezone);
			$end = $reservation->endDate->toTimezone($timezone);
			$referenceNumber = $reservation->referenceNumber;

			$cr = new RFCalendarReservation($start, $end, $resourceMap[$reservation->resourceId], $referenceNumber);
			$cr->seriesId = $reservation->reservationId;
			$cr->title = $reservation->title;
			$cr->ownerName = $reservation->fullName; //owner_name;
			$cr->ownerFirst = $reservation->fullName; //firstName;
			$cr->ownerLast = ''; //$reservation->lastName;
			$cr->displayTitle = $factory->format($reservation);//, Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION_LABELS,
					//ConfigKeys::RESERVATION_LABELS_RESOURCE_CALENDAR));

			$color = RFApplicationHelper::getReservationColor($cr->seriesId);
			if (!empty($color))
			{
				$cr->color = "#$color";
				$cr->textColor = new RFContrastingColor($color);
			}

			$cr->class = self::getClass($reservation);

			$res[] = $cr;
		}

		return $res;
	}

	private static function getClass(RFReservationItem/*View*/ $reservation)
	{
		if ($reservation->requiresApproval)
		{
			return 'reserved pending';
		}

		$user = JFactory::getUser();

		if ($reservation->isUserOwner($user->get('id')))
		{
			return 'reserved mine';
		}

		if ($reservation->isUserParticipating($user->get('id')))
		{
			return 'reserved participating';
		}

		return 'reserved';

	}
}