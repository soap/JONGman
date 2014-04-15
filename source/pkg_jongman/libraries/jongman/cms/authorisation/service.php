<?php
defined('_JEXEC') or die;

interface IRoleService
{
	/**
	 * @abstract
	 * @param User $user
	 * @return bool
	 */
	public function isApplicationAdministrator($user);

	/**
	 * @abstract
	 * @param User $user
	 * @return bool
	 */
	public function isResourceAdministrator($user);

	/**
	 * @abstract
	 * @param User $user
	 * @return bool
	 */
	public function isGroupAdministrator($user);

	/**
	 * @abstract
	 * @param User $user
	 * @return bool
	 */
	public function isScheduleAdministrator($user);
}

interface IAuthorisationService extends IRoleService
{
	/**
	 * @abstract
	 * @param UserSession $reserver user who is requesting access to perform action
	 * @return bool
	 */
	public function canReserveForOthers($reserver);

	/**
	 * @abstract
	 * @param UserSession $reserver user who is requesting access to perform action
	 * @param int $reserveForId user to reserve for
	 * @return bool
	 */
	public function canReserveFor($reserver, $reserveForId);

	/**
	 * @abstract
	 * @param UserSession $approver user who is requesting access to perform action
	 * @param int $approveForId user to approve for
	 * @return bool
	 */
	public function canApproveFor($approver, $approveForId);

    /**
     * @param UserSession $user
     * @param IResource $resource
     * @return bool
     */
    public function canEditForResource($user, IResource $resource);

    /**
     * @param UserSession $user
     * @param IResource $resource
     * @return bool
     */
    public function canApproveForResource($user, IResource $resource);

}

class RFAuthorisationService implements IAuthorisationService
{
	public static $_instance;
	protected function __construct(){}
	
	public function getInstance()
	{
		if (!self::$_instance) {
			self::$_instance = new RFAuthorisationService();	
		}
		return self::$_instance;
	}

	/**
	 * @param UserSession $reserver user who is requesting access to perform action
	 * @return bool
	 */
	public function canReserveForOthers($reserver)
	{
		if ($reserver->isAdmin)
		{
			return true;
		}

		return $user->isGroupAdmin();
	}

	/**
	 * @param UserSession $reserver user who is requesting access to perform action
	 * @param int $reserveForId user to reserve for
	 * @return bool
	 */
	public function canReserveFor($reserver, $reserveForId)
	{
		return $this->isAdminFor($reserver, $reserveForId);
	}

	/**
	 * @param UserSession $approver user who is requesting access to perform action
	 * @param int $approveForId user to approve for
	 * @return bool
	 */
	public function canApproveFor($approver, $approveForId)
	{
		return $this->isAdminFor($approver, $approveForId);
	}

    /**
     * @param User $user
     * @return bool
     */
    public function isApplicationAdministrator($user)
    {
        return $user->authorise('core.admin', 'com_jongman');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isResourceAdministrator($user)
    {
        return $user->authorise('core.admin', 'com_jongman');
    }

    /**
     * @param User $user
     * @return bool
     */
    public function isGroupAdministrator($user)
    {
        return $user->isInRole(RoleLevel::GROUP_ADMIN);
    }

	/**
     * @param User $user
     * @return bool
     */
    public function isScheduleAdministrator($user)
    {
        return $user->isInRole(RoleLevel::SCHEDULE_ADMIN);
    }

    /**
     * @param UserSession $userSession
     * @param IResource $resource
     * @return bool
     */
    public function canEditForResource($user, IResource $resource)
    {
       	if ($user->authorise('core.admin','com_jongman')) {
    		return true;
    	}

        if ($user->authorise('core.edit', 'com_jongman.resource.'.$resource->getResourceId())) {
    		return true;
    	}

        return $user->authorise('core.edit', 'com_jongman');
    }

    /**
     * @param $user
     * @param IResource $resource
     * @return bool
     */
    public function canApproveForResource($user, IResource $resource)
    {
    	
   		if ($user->authorise('core.admin','com_jongman')) {
    		return true;
    	}
    	
    	if ($user->authorise('core.edit', 'com_jongman.resource.'.$resource->getResourceId())) {
    		return true;
    	}
    	
    	if ($user->authorise('core.edit.state', 'com_jongman.resource.'.$resource->getResourceId())) {
    		return true;
    	}
    	
    	return false;
    }
}