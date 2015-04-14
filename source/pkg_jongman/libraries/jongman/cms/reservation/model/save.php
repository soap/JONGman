<?php

defined('_JEXEC') or die;


class RFReservationModelSave implements IReservationModel
{
	/**
	 * @var IReservationSavePage
	 */
	private $_cmsmodel;

	/**
	 * @var IReservationPersistenceService
	 */
	private $_persistenceService;

	/**
	 * @var IReservationHandler
	 */
	private $_handler;

	/**
	 * @var IResourceRepository
	 */
	private $_resourceRepository;

	public function __construct(
			IReservationSavePage $page,
			IReservationPersistenceService $persistenceService,
			IReservationHandler $handler,
			IResourceRepository $resourceRepository,
			UserSession $userSession)
	{
		$this->_page = $page;
		$this->_persistenceService = $persistenceService;
		$this->_handler = $handler;
		$this->_resourceRepository = $resourceRepository;
		$this->userSession = $userSession;
	}

	public function buildReservation()
	{
		$userId = $this->_page->getUserId();
		$primaryResourceId = $this->_page->getResourceId();
		$resource = $this->_resourceRepository->loadById($primaryResourceId);
		$title = $this->_page->getTitle();
		$description = $this->_page->getDescription();
		$roFactory = new RepeatOptionsFactory();
		$repeatOptions = $roFactory->CreateFromComposite($this->_page, $this->userSession->Timezone);
		$duration = $this->GetReservationDuration();

		$reservationSeries = RFReservationSeries::create($userId, $resource, $title, $description, $duration, $repeatOptions, $this->userSession);

		$resourceIds = $this->_page->getResources();
		foreach ($resourceIds as $resourceId)
		{
			if ($primaryResourceId != $resourceId)
			{
				$reservationSeries->AddResource($this->_resourceRepository->LoadById($resourceId));
			}
		}

		$accessories = $this->_page->GetAccessories();
		foreach ($accessories as $accessory)
		{
			$reservationSeries->AddAccessory(new ReservationAccessory($accessory->Id, $accessory->Quantity, $accessory->Name));
		}

		$attributes = $this->_page->GetAttributes();
		foreach ($attributes as $attribute)
		{
			$reservationSeries->AddAttributeValue(new AttributeValue($attribute->Id, $attribute->Value));
		}

		$participantIds = $this->_page->GetParticipants();
		$reservationSeries->ChangeParticipants($participantIds);

		$inviteeIds = $this->_page->GetInvitees();
		$reservationSeries->ChangeInvitees($inviteeIds);
		/*
		$attachments = $this->_page->GetAttachments();

		foreach($attachments as $attachment)
		{
			if ($attachment != null)
			{
				if ($attachment->IsError())
				{
					Log::Error('Error attaching file %s. %s', $attachment->OriginalName(), $attachment->Error());
				}
				else
				{
					$att = ReservationAttachment::Create($attachment->OriginalName(), $attachment->MimeType(), $attachment->Size(), $attachment->Contents(), $attachment->Extension(), 0);
					$reservationSeries->AddAttachment($att);
				}
			}
		}
		*/
		/*
		if ($this->_page->HasStartReminder())
		{
			$reservationSeries->AddStartReminder(new ReservationReminder($this->_page->GetStartReminderValue(), $this->_page->GetStartReminderInterval()));
		}

		if ($this->_page->HasEndReminder())
		{
			$reservationSeries->AddEndReminder(new ReservationReminder($this->_page->GetEndReminderValue(), $this->_page->GetEndReminderInterval()));
		}
		*/
		return $reservationSeries;
	}

	/**
	 * @param ReservationSeries $reservationSeries
	 */
	public function handleReservation($reservationSeries)
	{
		$successfullySaved = $this->_handler->handle(
				$reservationSeries,
				$this->_page);


		if ($successfullySaved)
		{
			$this->_page->setRequiresApproval($reservationSeries->requiresApproval());
			$this->_page->setReferenceNumber($reservationSeries->currentInstance()->referenceNumber());
		}
	}

	/**
	 * @return RFDateRange
	 */
	private function getReservationDuration()
	{
		$startDate = $this->_page->getStartDate();
		$startTime = $this->_page->getStartTime();
		$endDate = $this->_page->getEndDate();
		$endTime = $this->_page->getEndTime();

		$timezone = $this->userSession->timezone;
		return RFDateRange::create($startDate . ' ' . $startTime, $endDate . ' ' . $endTime, $timezone);
	}
}