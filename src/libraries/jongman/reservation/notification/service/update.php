<?php
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