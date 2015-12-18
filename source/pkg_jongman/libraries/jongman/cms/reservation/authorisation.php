<?php
defined('_JEXEC') or die;

interface IReservationAuthorisation
{
	/**
	 * @abstract
	 * @param UserSession $currentUser
	 * @return bool
	 */
	function canChangeUsers(JUser $currentUser);

	/**
	 * @abstract
	 * @param ReservationView $reservationView
	 * @param UserSession $currentUser
	 * @return bool
	*/
	function canEdit(RFReservationView $reservationView, JUser $currentUser);

	/**
	 * @abstract
	 * @param ReservationView $reservationView
	 * @param UserSession $currentUser
	 * @return bool
	*/
	function canApprove(RFReservationView $reservationView, JUser $currentUser);

	/**
	 * @abstract
	 * @param ReservationView $reservationView
	 * @param UserSession $currentUser
	 * @return bool
	*/
	function canViewDetails(RFReservationView $reservationView, JUser $currentUser);
}

class RFReservationAuthorisation implements IReservationAuthorisation
{
	/**
	 * @var \IAuthorizationService
	 */
	private $authorizationService;

	public function __construct(IAuthorizationService $authorizationService)
	{
		$this->authorizationService = $authorizationService;
	}

	public function canEdit(RFReservationView $reservationView, JUser $currentUser)
	{
		$ongoingReservation = true;
		$params = JComponentHelper::getParams('com_jongman');
		$startTimeConstraint = $params->get('startTimeContraint');

		if ($startTimeConstraint == RFReservationStartTimeConstraint::CURRENT)
		{
			$ongoingReservation = RFDate::now()->lessThan($reservationView->endDate);
		}

		if ($startTimeConstraint == RFReservationStartTimeConstraint::FUTURE)
		{
			$ongoingReservation = RFDate::now()->lessThan($reservationView->startDate);
		}

		if ($ongoingReservation)
		{
			if ($this->isAccessibleTo($reservationView, $currentUser))
			{
				return true;
			}
		}

		return $currentUser->authorise('core.admin','com_jongamn');	// only admins can edit reservations that have ended
	}

	public function canChangeUsers(JUser $currentUser)
	{
		return $currentUser->authorise('core.create', 'com_jongman') || $this->authorizationService->canReserveForOthers($currentUser);
	}

	public function canApprove(RFReservationView $reservationView, JUser $currentUser)
	{
		if (!$reservationView->requiresApproval())
		{
			return false;
		}

		if ($currentUser->authorise('core.create', 'com_jongman'))
		{
			return true;
		}

		$canReserveForUser = $this->authorizationService->canApproveFor($currentUser, $reservationView->ownerId);
		if ($canReserveForUser)
		{
			return true;
		}

		foreach ($reservationView->resources as $resource)
		{
			if ($this->authorizationService->canApproveForResource($currentUser, $resource))
			{
				return true;
			}
		}

		return false;
	}

	public function canViewDetails(RFReservationView $reservationView, JUser $currentUser)
	{
		return $this->isAccessibleTo($reservationView, $currentUser);
	}

	/**
	 * @param RFReservationView $reservationView
	 * @param UserSession $currentUser
	 * @return bool
	 */
	private function isAccessibleTo(RFReservationView $reservationView, JUser $currentUser)
	{
		if ($reservationView->ownerId == $currentUser->get('id') || $currentUser->authorise('core.create', 'com_jongman'))
		{
			return true;
		}
		else
		{
			$canReserveForUser = $this->authorizationService->canReserveFor($currentUser, $reservationView->ownerId);
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