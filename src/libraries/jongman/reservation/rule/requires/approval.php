<?php
defined('_JEXEC') or die;


class RFReservationRuleRequiresApproval implements IReservationValidationRule
{
	/**
	 * @var IAuthorizationService
	 */
	private $authorisationService;
	
	public function __construct(IAuthorisationService $authorisationService)
	{
		$this->authorisationService = $authorisationService;
	}
	
	/**
	 * @param ReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	function validate($reservationSeries)
	{
		$status = 1; //ReservationStatus::Created;
	
		/** @var RFResourceBookable $resource */
		foreach ($reservationSeries->allResources() as $resource)
		{
			if ($resource->getRequiresApproval())
			{
				if (!$this->authorisationService->canApproveForResource($reservationSeries->bookedBy(), $resource))
				{
					$status = -1; //pending
					break;
				}
			}
		}
	
		$reservationSeries->setStatusId($status);
	
		return new RFReservationRuleResult();
	}
	
	public function getError()
	{
		return '';
	}
}