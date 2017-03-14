<?php
class RFReservationNotificationServiceApprove extends RFReservationNotificationService
{
	public function __construct(IUserRepository $userRepo, IResourceRepository $resourceRepo, IAttributeRepository $attributeRepo)
	{
		$notifications = array();
		//$notifications[] = new OwnerEmailApprovedNotification($userRepo, $attributeRepo);

		parent::__construct($notifications);
	}
}