<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IPostReservationFactory
{
	/**
	 * @param UserSession $userSession
	 * @return IReservationNotificationService
	 */
	public function createPostAddService($user);

	/**
	 * @param UserSession $userSession
	 * @return IReservationNotificationService
	*/
	public function createPostUpdateService($user);

	/**
	 * @param UserSession $userSession
	 * @return IReservationNotificationService
	*/
	public function createPostDeleteService($user);

	/**
	 * @param UserSession $userSession
	 * @return IReservationNotificationService
	*/
	public function createPostApproveService($user);
}

class RFFactoryPostReservation implements IPostReservationFactory
{

	/**
	 * @param UserSession $userSession
	 * @return IReservationNotificationService
	 */
	public function createPostAddService($user)
	{
		return new RFReservationNotificationServiceAdd(new RFUserRepository(), new RFResourceRepository(), new RFAttributeRepository());
	}

	/**
	 * @param UserSession $userSession
	 * @return IReservationNotificationService
	 */
	public function createPostUpdateService($user)
	{
		return new RFReservationNotificationServiceUpdate(new RFUserRepository(), new RFResourceRepository(), new RFAttributeRepository());
	}

	/**
	 * @param UserSession $userSession
	 * @return IReservationNotificationService
	 */
	public function createPostDeleteService($user)
	{
		return new RFReservationNotificationServiceDelete(new RFUserRepository(), new RFResourceRepository(), new RFAttributeRepository());
	}

	/**
	 * @param UserSession $userSession
	 * @return IReservationNotificationService
	 */
	public function createPostApproveService($user)
	{
		return new RFReservationNotificationServiceApprove(new RFUserRepository(), new RFResourceRepository(), new RFAttributeRepository());
	}
}