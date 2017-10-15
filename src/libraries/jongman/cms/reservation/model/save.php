<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationModelSave implements IReservationModel
{
	/**
	 * @var IReservationSavePage
	 */
	private $_page;

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
	
	
	private $_user;

	public function __construct(
			IReservationPage $page,
			IReservationPersistenceService $persistenceService,
			IReservationHandler $handler,
			IResourceRepository $resourceRepository,
			JUser $user)
	{
		$this->_page = $page;
		$this->_persistenceService = $persistenceService;
		$this->_handler = $handler;
		$this->_resourceRepository = $resourceRepository;
		$this->_user = $user;
	}

	public function buildReservation()
	{
		$userId = $this->_page->getUserId();
		$primaryResourceId = $this->_page->getResourceId();
		$resource = $this->_resourceRepository->loadById($primaryResourceId);
		$title = $this->_page->getTitle();
		$description = $this->_page->getDescription();
		$roFactory = new RFReservationRepeatOptionsFactory();
		
		$config = JFactory::getConfig();
		$repeatOptions = $roFactory->createFromComposite($this->_page, $this->_user->getParam('timezone', $config->get('config.offset') ));
		$duration = $this->getReservationDuration();

		$reservationSeries = RFReservationSeries::create($userId, $resource, $title, $description, $duration, $repeatOptions, $this->_user);

		$resourceIds = $this->_page->getResources();
		foreach ($resourceIds as $resourceId)
		{
			if ($primaryResourceId != $resourceId)
			{
				$reservationSeries->addResource($this->_resourceRepository->loadById($resourceId));
			}
		}

		$accessories = $this->_page->getAccessories();
		foreach ($accessories as $accessory)
		{
			$reservationSeries->addAccessory(new RFReservationAccessory($accessory->id, $accessory->quantity, $accessory->name));
		}

		$attributes = $this->_page->getAttributes();
		foreach ($attributes as $attribute)
		{
			$reservationSeries->addAttributeValue(new AttributeValue($attribute->id, $attribute->value));
		}

		$participantIds = $this->_page->getParticipants();
		$reservationSeries->changeParticipants($participantIds);

		$inviteeIds = $this->_page->getInvitees();
		$reservationSeries->changeInvitees($inviteeIds);
		/*
		$attachments = $this->_page->getAttachments();

		foreach($attachments as $attachment)
		{
			if ($attachment != null)
			{
				if ($attachment->isError())
				{
					JLog::add('Error attaching file %s. %s', $attachment->OriginalName(), $attachment->Error());
				}
				else
				{
					$att = ReservationAttachment::Create($attachment->OriginalName(), $attachment->MimeType(), $attachment->Size(), $attachment->Contents(), $attachment->Extension(), 0);
					$reservationSeries->addAttachment($att);
				}
			}
		}
		*/
		/*
		if ($this->_page->hasStartReminder())
		{
			$reservationSeries->addStartReminder(new ReservationReminder($this->_page->getStartReminderValue(), $this->_page->getStartReminderInterval()));
		}

		if ($this->_page->hasEndReminder())
		{
			$reservationSeries->addEndReminder(new ReservationReminder($this->_page->getEndReminderValue(), $this->_page->getEndReminderInterval()));
		}
		*/
		return $reservationSeries;
	}

	/**
	 * @param RFReservationSeries $reservationSeries
	 */
	public function handleReservation($reservationSeries)
	{
		$successfullySaved = $this->_handler->handle($reservationSeries, $this->_page);

		if ($successfullySaved)
		{
			//$this->_page->setRequiresApproval($reservationSeries->requiresApproval());
			//$this->_page->setReferenceNumber($reservationSeries->currentInstance()->referenceNumber());
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