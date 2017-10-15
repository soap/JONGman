<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IReservationModelUpdate
{
	/**
	 * @return RFReservationExistingSeries
	 */
	public function buildReservation();

	/**
	 * @param RFReservationExistingSeries $reservationSeries
	*/
	public function handleReservation($reservationSeries);
}

class RFReservationModelUpdate implements IReservationModelUpdate
{
	/**
	 * @var IReservationPage
	 */
	private $page;

	/**
	 * @var UpdateReservationPersistenceService
	 */
	private $persistenceService;

	/**
	 * @var IReservationHandler
	 */
	private $handler;

	/**
	 * @var IResourceRepository
	 */
	private $resourceRepository;

	public function __construct(
			IReservationPage $page,
			IUpdateReservationPersistenceService $persistenceService,
			IReservationHandler $handler,
			IResourceRepository $resourceRepository,
			JUser $user)
	{
		$this->page = $page;
		$this->persistenceService = $persistenceService;
		$this->handler = $handler;
		$this->resourceRepository = $resourceRepository;
		$this->user = $user;
	}

	/**
	 * @return RFReservationExistingSeries
	 */
	public function buildReservation()
	{
		$referenceNumber = $this->page->getReferenceNumber();
		$existingSeries = $this->persistenceService->loadByReferenceNumber($referenceNumber);
		$existingSeries->applyChangesTo($this->page->getSeriesUpdateScope());

		$resourceId = $this->page->getResourceId();
		$additionalResourceIds = $this->page->getResources();

		if (empty($resourceId))
		{
			// the first additional resource will become the primary if the primary is removed
			$resourceId = array_shift($additionalResourceIds);
		}

		$resource = $this->resourceRepository->loadById($resourceId);

		$existingSeries->update(
				$this->page->getUserId(),
				$resource,
				$this->page->getTitle(),
				$this->page->getDescription(),
				$this->user,
				$this->page->getCustomerId());

		$existingSeries->updateDuration($this->getReservationDuration());
		
		$roFactory = new RFReservationRepeatOptionsFactory();

		$existingSeries->repeats($roFactory->createFromComposite($this->page, $this->user->getParam('timezone') ));
		
		$additionalResources = array();
		foreach ($additionalResourceIds as $additionalResourceId)
		{
			if ($additionalResourceId != $resourceId)
			{
				$additionalResources[] = $this->resourceRepository->loadById($additionalResourceId);
			}
		}

		$existingSeries->changeResources($additionalResources);
		/*
		$existingSeries->changeParticipants($this->page->getParticipants());
		$existingSeries->changeInvitees($this->page->getInvitees());
		$existingSeries->changeAccessories($this->getAccessories());
		$existingSeries->changeAttributes($this->getAttributes());
		
		$attachments = $this->page->getAttachments();
		foreach ($attachments as $attachment)
		{
			if ($attachment != null)
			{
				if ($attachment->isError())
				{
					JLog::add('Error attaching file %s. %s', $attachment->originalName(), $attachment->Error());
				}
				else
				{
					JLog::add('Attaching file %s to series %s', $attachment->originalName(), $existingSeries->seriesId(), JLog::DEBUG);
					$att = RFReservationAttachment::create($attachment->originalName(), $attachment->mimeType(),
							$attachment->size(), $attachment->contents(),
							$attachment->extension(), $existingSeries->seriesId());
					$existingSeries->addAttachment($att);
				}
			}
		}
		*/
		/*
		foreach ($this->page->getRemovedAttachmentIds() as $fileId)
		{
			$existingSeries->removeAttachment($fileId);
		}
		*/
		/*
		if ($this->page->hasStartReminder())
		{
			$existingSeries->addStartReminder(new RFReservationReminder($this->page->getStartReminderValue(), $this->page->getStartReminderInterval()));
		}
		else
		{
			$existingSeries->removeStartReminder();
		}
	
		if ($this->page->hasEndReminder())
		{
			$existingSeries->addEndReminder(new RFReservationReminder($this->page->getEndReminderValue(), $this->page->getEndReminderInterval()));
		}
		else
		{
			$existingSeries->removeEndReminder();
		}
		*/
		return $existingSeries;
	}

	/**
	 * @param RFReservationExistingSeries $reservationSeries
	 */
	public function handleReservation($reservationSeries)
	{
		$successfullySaved = $this->handler->handle($reservationSeries, $this->page);

		if ($successfullySaved)
		{
			//$this->page->setRequiresApproval($reservationSeries->requiresApproval());
			//$this->page->setReferenceNumber($reservationSeries->currentInstance()->referenceNumber());
		}
		
	}

	/**
	 * @return RFDateRange
	 */
	private function getReservationDuration()
	{
		$startDate = $this->page->getStartDate();
		$startTime = $this->page->getStartTime();
		$endDate = $this->page->getEndDate();
		$endTime = $this->page->getEndTime();

		$config = JFactory::getConfig();
		$timezone = $this->user->getParam('timezone', $config->get('config.offset'));
		return RFDateRange::create($startDate . ' ' . $startTime, $endDate . ' ' . $endTime, $timezone);
	}

	/**
	 * Get attribute values from page 
	 * @return multitype:RFReservationAccessory
	 */
	private function getAccessories()
	{
		$accessories = array();
		foreach ($this->page->getAccessories() as $accessory)
		{
			$accessories[] = new RFReservationAccessory($accessory->id, $accessory->quantity, $accessory->name);
		}

		return $accessories;
	}

	/**
	 * @return AttributeValue[]
	 */
	private function getAttributes()
	{
		$attributes = array();
		foreach ($this->page->getAttributes() as $attribute)
		{
			$attributes[] = new RFAttributeValue($attribute->id, $attribute->value);
		}

		return $attributes;
	}
}