<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationNotificationServiceDelete extends RFReservationNotificationService
{
	public function __construct(IUserRepository $userRepo, IResourceRepository $resourceRepo, IAttributeRepository $attributeRepo)
	{
		$notifications = array();

		//$notifications[] = new OwnerEmailDeletedNotification($userRepo, $attributeRepo);
		//$notifications[] = new ParticipantDeletedEmailNotification($userRepo, $attributeRepo);
		//$notifications[] = new AdminEmailDeletedNotification($userRepo, $userRepo, $attributeRepo);

		parent::__construct($notifications);
	}
}