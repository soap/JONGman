<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationPermissionRule implements IReservationValidationRule
{
	/**
	 * @var IPermissionServiceFactory
	 */
	private $permissionServiceFactory;

	public function __construct(IPermissionServiceFactory $permissionServiceFactory)
	{
		$this->permissionServiceFactory = $permissionServiceFactory;
	}

	/**
	 * @param ReservationSeries $reservation
	 * @return ReservationRuleResult
	 */
	public function validate($reservation)
	{
		$reservation->userId();
		
		$permissionService = $this->permissionServiceFactory->GetPermissionService();

		$resourceIds = $reservation->allResourceIds();

		foreach ($resourceIds as $resourceId)
		{
			if (!$permissionService->CanAccessResource(new ReservationResource($resourceId), $reservation->BookedBy()))
			{
				return new RFReservationRuleResult(false, JText::_('COM_JONGMAN_NO_RESOURCE_PERMISSION'));
			}
		}

		return new RFReservationRuleResult(true);
	}
}