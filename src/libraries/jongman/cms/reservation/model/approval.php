<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
jimport('jongman.reservation.view.view');
jimport('jongman.cms.authorisation.service');

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
			/*IReservationApprovalPage $page,*/
			IReservationPage $page, // Use IReservationPage instead as we create it in one place not separate yet
 			IUpdateReservationPersistenceService $persistenceService,
			IReservationHandler $handler,
			IReservationAuthorisation $authorizationService,
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

		JLog::add('User: %s, Approving reservation with reference number %s', $this->userSession->id, $referenceNumber);

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

		//foreach($series->addedAttachments() as $attachment)
		//{
			//$this->Attachments[] = new RFReservationAttachmentView($attachment->FileId(), $series->SeriesId(), $attachment->FileName());
		//}

		//foreach($series->attributeValues() as $av)
		//{
		//	$this->attributes[] = $av;
		//}

		$this->description = $series->getDescription();
		$this->endDate = $series->currentInstance()->endDate();
		$this->ownerId = $series->userId();
		$this->referenceNumber = $series->currentInstance()->referenceNumber();
		$this->reservationId = $series->currentInstance()->reservationId();
		$this->resourceId = $series->resourceId();

		foreach($series->allResources() as $resource)
		{
			if ($resource->isOnline()) {
				$resourceStatus = RFResourceStatus::AVAILABLE;
			}else{
				$resourceStatus = RFResourceStatus::UNAVAILABLE;
			}
			$this->resources[] = new RFResourceReservationView($resource->getId(), $resource->getName(), $resource->getAdminGroupId(), $resource->getScheduleId(), $resource->getScheduleAdminGroupId(), $resourceStatus);
		}

		
		$this->scheduleId = $series->scheduleId();
		$this->seriesId = $series->seriesId();
		$this->startDate = $series->currentInstance()->startDate();
		$this->statusId = $series->statusId();
	}
}