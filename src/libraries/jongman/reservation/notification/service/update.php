<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
class RFReservationNotificationServiceUpdate extends RFReservationNotificationService
{
	public function __construct(IUserRepository $userRepo, IResourceRepository $resourceRepo, IAttributeRepository $attributeRepo)
	{
		$notifications = array();
		//$notifications[] = new OwnerEmailUpdatedNotification($userRepo, $attributeRepo);
		//$notifications[] = new AdminEmailUpdatedNotification($userRepo, $userRepo, $attributeRepo);
		//$notifications[] = new ParticipantAddedEmailNotification($userRepo, $attributeRepo);
		//$notifications[] = new InviteeAddedEmailNotification($userRepo, $attributeRepo);

		parent::__construct($notifications);
	}
}