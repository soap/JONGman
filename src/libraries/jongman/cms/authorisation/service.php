<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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

interface IAuthorisationService extends IRoleService
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

class RFAuthorisationService implements IAuthorisationService
{
	/**
	 * @var IUserRepository
	 */
	private $userRepository;

	public function __construct(JUser $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	/**
	 * @param UserSession $reserver user who is requesting access to perform action
	 * @return bool
	 */
	public function canReserveForOthers(JUser $reserver)
	{
		if ($reserver->authorise('core_admin', 'com_jongman'))
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
	public function canReserveFor(JUser $reserver, $reserveForId)
	{
		return $this->isAdminFor($reserver, $reserveForId);
	}

	/**
	 * @param UserSession $approver user who is requesting access to perform action
	 * @param int $approveForId user to approve for
	 * @return bool
	 */
	public function canApproveFor(JUser $approver, $approveForId)
	{
		return $this->isAdminFor($approver, $approveForId);
	}

    /**
     * @param User $user
     * @return bool
     */
    public function isApplicationAdministrator(JUser $user)
    {
        if ($user->EmailAddress() == Configuration::Instance()->GetKey(ConfigKeys::ADMIN_EMAIL))
        {
            return true;
        }

        return $user->authorise('core.admin', 'com_jongman');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isResourceAdministrator(JUser $user)
    {
        return $user->authroise('core.admin', 'com_jongman');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isGroupAdministrator(JUser $user)
    {
        return $user->authorise('core.admin', 'com_jongamn');
    }

	/**
     * @param User $user
     * @return bool
     */
    public function isScheduleAdministrator(JUser $user)
    {
        return $user->$user->authorise('core.admin', 'com_jongamn');
    }

	/**
	 * @param UserSession $userSession
	 * @param int $otherUserId
	 * @return bool
	 */
	private function isAdminFor(JUser $userSession, $otherUserId)
	{
		if ($userSession->authorise('core.admin'))
		{
			return true;
		}

        if (!$userSession->authorise('core.admin', 'com_jongman'))
        {
            // dont even bother checking if the user isnt a group admin
            return false;
        }

		//$user1 = $this->userRepository->LoadById($usefarSession->UserId);
		//$user2 = $this->userRepository->LoadById($otherUserId);

		return false;//$user1->IsAdminFor($user2);
	}

    /**
     * @param UserSession $userSession
     * @param IResource $resource
     * @return bool
     */
    public function canEditForResource(JUser $userSession, IResource $resource)
    {
        if ($userSession->authorise('core.admin', 'com_jongman'))
        {
            return true;
        }

        if (!$userSession->isResourceAdmin && !$userSession->IsScheduleAdmin)
        {
            return false;
        }

        $user = $this->userRepository->LoadById($userSession->userId);

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