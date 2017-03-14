<?php

interface IReservationApprovalModel
{
	public function approve();
}

class RFReservationModelApproval implements IReservationApprovalModel
{
	/**
	 * @var IReservationApprovalPage
	 */
	private $page;

	/**
	 * @var IUpdateReservationPersistenceService
	 */
	private $persistenceService;

	/**
	 * @var IReservationHandler
	 */
	private $handler;

	/**
	 * @var IReservationAuthorization
	 */
	private $authorization;

	/**
	 * @var UserSession
	 */
	private $userSession;

	public function __construct(
			IReservationApprovalPage $page,
			IUpdateReservationPersistenceService $persistenceService,
			IReservationHandler $handler,
			IReservationAuthorization $authorizationService,
			JUser $userSession)
	{
		$this->page = $page;
		$this->persistenceService = $persistenceService;
		$this->handler = $handler;
		$this->authorization = $authorizationService;
		$this->userSession = $userSession;
	}

	public function approve()
	{
		$referenceNumber = $this->page->getReferenceNumber();

		//JLog::add('User: %s, Approving reservation with reference number %s', $this->userSession->UserId, $referenceNumber);

		$series = $this->persistenceService->loadByReferenceNumber($referenceNumber);
		if($this->authorization->canApprove(new RFReservationViewAdapter($series), $this->userSession))
		{
			$series->approve($this->userSession);
			$this->handler->handle($series, $this->page);
		}
	}
}


class RFReservationViewAdapter extends RFReservationView
{
	public function __construct(RFReservationExistingSeries $series)
	{
		foreach ($series->accessories() as $accessory)
		{
			//$this->accessories[] = new ReservationAccessoryView($accessory->AccessoryId, $accessory->QuantityReserved, $accessory->Name, null);
		}

		foreach($series->additionalResources() as $resource)
		{
			$this->additionalResourceIds[] = $resource->getId();
		}

		foreach($series->addedAttachments() as $attachment)
		{
			//$this->Attachments[] = new RFReservationAttachmentView($attachment->FileId(), $series->SeriesId(), $attachment->FileName());
		}

		foreach($series->attributeValues() as $av)
		{
			$this->attributes[] = $av;
		}

		$this->description = $series->description();
		$this->endDate = $series->currentInstance()->endDate();
		$this->ownerId = $series->userId();
		$this->referenceNumber = $series->currentInstance()->referenceNumber();
		$this->reservationId = $series->currentInstance()->reservationId();
		$this->resourceId = $series->resourceId();

		foreach($series->allResources() as $resource)
		{
			$this->resources[] = new RFReservationResourceView($resource->getId(), $resource->getName(), $resource->getAdminGroupId(), $resource->getScheduleId(), $resource->getScheduleAdminGroupId(), $resource->getStatusId());
		}

		$this->scheduleId = $series->scheduleId();
		$this->seriesId = $series->seriesId();
		$this->startDate = $series->currentInstance()->startDate();
		$this->statusId = $series->statusId();
	}
}