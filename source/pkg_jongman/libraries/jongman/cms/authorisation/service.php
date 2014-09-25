<?php
defined('_JEXEC') or die;


interface IRoleService
{
	/**
	 * @abstract
	 * @param User $user
	 * @return bool
	 */
	public function isApplicationAdministrator(JUser $user);

	/**
	 * @abstract
	 * @param User $user
	 * @return bool
	 */
	public function isResourceAdministrator(JUser $user);

	/**
	 * @abstract
	 * @param User $user
	 * @return bool
	 */
	public function isGroupAdministrator(JUser $user);

	/**
	 * @abstract
	 * @param User $user
	 * @return bool
	 */
	public function isScheduleAdministrator(JUser $user);
}

interface IAuthorizationService extends IRoleService
{
	/**
	 * @abstract
	 * @param UserSession $reserver user who is requesting access to perform action
	 * @return bool
	 */
	public function canReserveForOthers(JUser $reserver);

	/**
	 * @abstract
	 * @param UserSession $reserver user who is requesting access to perform action
	 * @param int $reserveForId user to reserve for
	 * @return bool
	 */
	public function canReserveFor(JUser $reserver, $reserveForId);

	/**
	 * @abstract
	 * @param UserSession $approver user who is requesting access to perform action
	 * @param int $approveForId user to approve for
	 * @return bool
	 */
	public function canApproveFor(JUser $approver, $approveForId);

    /**
     * @param UserSession $user
     * @param IResource $resource
     * @return bool
     */
    public function canEditForResource(JUser $user, IResource $resource);

    /**
     * @param UserSession $user
     * @param IResource $resource
     * @return bool
     */
    public function canApproveForResource(JUser $user, IResource $resource);

}

class RFAuthorizationService implements IAuthorizationService
{
	/**
	 * @var IUserRepository
	 */
	private $userRepository;

	public function __construct(JFactory $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @param UserSession $reserver user who is requesting access to perform action
	 * @return bool
	 */
	public function canReserveForOthers(JUser $reserver)
	{
		if ($reserver->isAdmin)
		{
			return true;
		}

		$user = $this->userRepository->getUser($reserver->id);

		return $user->IsGroupAdmin();
	}

	/**
	 * @param UserSession $reserver user who is requesting access to perform action
	 * @param int $reserveForId user to reserve for
	 * @return bool
	 */
	public function canReserveFor(UserSession $reserver, $reserveForId)
	{
		return $this->isAdminFor($reserver, $reserveForId);
	}

	/**
	 * @param UserSession $approver user who is requesting access to perform action
	 * @param int $approveForId user to approve for
	 * @return bool
	 */
	public function canApproveFor(UserSession $approver, $approveForId)
	{
		return $this->isAdminFor($approver, $approveForId);
	}

    /**
     * @param User $user
     * @return bool
     */
    public function isApplicationAdministrator(User $user)
    {
        if ($user->EmailAddress() == Configuration::Instance()->GetKey(ConfigKeys::ADMIN_EMAIL))
        {
            return true;
        }

        return $user->isInRole(RoleLevel::APPLICATION_ADMIN);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function IsResourceAdministrator(User $user)
    {
        return $user->IsInRole(RoleLevel::RESOURCE_ADMIN);
    }

    /**
     * @param User $user
     * @return bool
     */
    public function IsGroupAdministrator(User $user)
    {
        return $user->IsInRole(RoleLevel::GROUP_ADMIN);
    }

	/**
     * @param User $user
     * @return bool
     */
    public function IsScheduleAdministrator(User $user)
    {
        return $user->IsInRole(RoleLevel::SCHEDULE_ADMIN);
    }

	/**
	 * @param UserSession $userSession
	 * @param int $otherUserId
	 * @return bool
	 */
	private function IsAdminFor(UserSession $userSession, $otherUserId)
	{
		if ($userSession->IsAdmin)
		{
			return true;
		}

        if (!$userSession->IsGroupAdmin)
        {
            // dont even bother checking if the user isnt a group admin
            return false;
        }

		$user1 = $this->userRepository->LoadById($userSession->UserId);
		$user2 = $this->userRepository->LoadById($otherUserId);

		return $user1->IsAdminFor($user2);
	}

    /**
     * @param UserSession $userSession
     * @param IResource $resource
     * @return bool
     */
    public function CanEditForResource(UserSession $userSession, IResource $resource)
    {
        if ($userSession->IsAdmin)
        {
            return true;
        }

        if (!$userSession->IsResourceAdmin && !$userSession->IsScheduleAdmin)
        {
            return false;
        }

        $user = $this->userRepository->LoadById($userSession->UserId);

        return $user->IsResourceAdminFor($resource);
    }

    /**
     * @param UserSession $userSession
     * @param IResource $resource
     * @return bool
     */
    public function canApproveForResource(JUser $userSession, IResource $resource)
    {
        if ($userSession->IsAdmin)
        {
            return true;
        }

        if (!$userSession->IsResourceAdmin)
        {
            return false;
        }

        $user = $this->userRepository->LoadById($userSession->UserId);

        return $user->IsResourceAdminFor($resource);
    }
}