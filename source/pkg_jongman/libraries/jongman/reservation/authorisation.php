<?php
defined('_JEXEC') or die;

class RFReservationAuthorization implements IReservationAuthorization
{
	/**
	 * @var \IAuthorizationService
	 */
	private $authorizationService;

	public function __construct(IAuthorizationService $authorizationService)
	{
		$this->authorizationService = $authorizationService;
	}

	public function canEdit(ReservationView $reservationView, UserSession $currentUser)
	{
		$ongoingReservation = true;
		$startTimeConstraint = Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_START_TIME_CONSTRAINT);

		if ($startTimeConstraint == ReservationStartTimeConstraint::CURRENT)
		{
			$ongoingReservation = Date::Now()->LessThan($reservationView->EndDate);
		}

		if ($startTimeConstraint == ReservationStartTimeConstraint::FUTURE)
		{
			$ongoingReservation = Date::Now()->LessThan($reservationView->StartDate);
		}

		if ($ongoingReservation)
		{
			if ($this->IsAccessibleTo($reservationView, $currentUser))
			{
				return true;
			}
		}

		return $currentUser->IsAdmin;	// only admins can edit reservations that have ended
	}

	public function canChangeUsers(UserSession $currentUser)
	{
		return $currentUser->IsAdmin || $this->authorizationService->CanReserveForOthers($currentUser);
	}

	public function canApprove(ReservationView $reservationView, UserSession $currentUser)
	{
		if (!$reservationView->RequiresApproval())
		{
			return false;
		}

		if ($currentUser->IsAdmin)
		{
			return true;
		}

		$canReserveForUser = $this->authorizationService->CanApproveFor($currentUser, $reservationView->OwnerId);
		if ($canReserveForUser)
		{
			return true;
		}

		foreach ($reservationView->Resources as $resource)
		{
			if ($this->authorizationService->CanApproveForResource($currentUser, $resource))
			{
				return true;
			}
		}

		return false;
	}

	public function canViewDetails(ReservationView $reservationView, UserSession $currentUser)
	{
		return $this->IsAccessibleTo($reservationView, $currentUser);
	}

	/**
	 * @param ReservationView $reservationView
	 * @param UserSession $currentUser
	 * @return bool
	 */
	private function isAccessibleTo(ReservationView $reservationView, UserSession $currentUser)
	{
		if ($reservationView->ownerId == $currentUser->get('id') || $currentUser->authorise('core.adm', 'com_jongman'))
		{
			return true;
		}
		else
		{
			$canReserveForUser = $this->authorizationService->CanReserveFor($currentUser, $reservationView->OwnerId);
			if ($canReserveForUser)
			{
				return true;
			}

			foreach ($reservationView->resources as $resource)
			{
				if ($this->authorizationService->canEditForResource($currentUser, $resource))
				{
					return true;
				}
			}
		}

		return false;
	}
}
