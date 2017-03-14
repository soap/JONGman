<?php
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