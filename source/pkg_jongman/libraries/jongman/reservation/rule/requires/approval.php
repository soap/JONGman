<?php
defined('_JEXEC') or die;


class RFReservationRuleRequiresApproval implements IReservationValidationRule
{
	/**
	 * @var IAuthorizationService
	 */
	private $authorizationService;
	
	public function __construct(IAuthorisationService $authorisationService)
	{
		$this->authorisationService = $authorisationService;
	}
	
	public function getError() {}
	/**
	 * @param ReservationSeries $reservationSeries
	 * @return ReservationRuleResult
	 */
	function validate($reservationSeries)
	{
		$status = RFReservationStatus::created;
		
		/** @var RFResourceBookable $resource */
		foreach ($reservationSeries->allResources() as $resource)
		{
			JLog::add(" resource id : {$resource->getResourceId()}", JLog::DEBUG, 'validation');
			if ($resource->getRequiresApproval())
			{
				JLog::add("    it requires approval ", JLog::DEBUG, 'validation');
				if (!$this->authorisationService->canApproveForResource($reservationSeries->bookedBy(), $resource))
				{
					$status = RFReservationStatus::pending;
					break;
				}
			}
		}
		JLog::add("Got final reservation staus : {$status}", JLog::DEBUG, 'validation');
		$reservationSeries->setStatusId($status);
	
		return new RFReservationRuleResult();
	}
}