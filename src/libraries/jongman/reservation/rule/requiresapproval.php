<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFValidationRuleRequiresapproval implements IReservationValidationRule
{
	/**
	 * @var IAuthorizationService
	 */
	private $authorizationService;
	
	public function __construct(IAuthorizationService $authorizationService)
	{
		$this->authorizationService = $authorizationService;
	}
	
	/**
	 * @param ReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	function validate($reservationSeries)
	{
		$status = 0; //ReservationStatus::Created;
	
		/** @var RFResourceBookable $resource */
		foreach ($reservationSeries->allResources() as $resource)
		{
			if ($resource->GetRequiresApproval())
			{
				if (!$this->authorizationService->canApproveForResource($reservationSeries->bookedBy(), $resource))
				{
					$status = -1; //pending
					break;
				}
			}
		}
	
		$reservationSeries->setStatusId($status);
	
		return new RFReservationRuleResult();
	}
}