<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('jongman.cms.resource.repository');
jimport('jongman.cms.reservation.view.repository');
jimport('jongman.cms.schedule.repository');
jimport('jongman.cms.user.repository');

class RFFactoryPreReservation implements IPreReservationFactory
{
	/**
	 * @var ResourceRepository
	 */
	protected $resourceRepository;

	/**
	 * @var ReservationViewRepository
	 */
	protected $reservationRepository;

	/**
	 * @var ScheduleRepository
	 */
	protected $scheduleRepository;

	/**
	 * @var UserRepository
	 */
	protected $userRepository;

	public function __construct()
	{
		$this->resourceRepository = new RFResourceRepository();
		$this->reservationRepository = new RFReservationViewRepository();
		$this->scheduleRepository = new RFScheduleRepository();
		$this->userRepository = new RFUserRepository();
	}

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	public function createPreAddService($user)
	{
		return $this->createAddService($this->getAddUpdateRuleProcessor($user), $user);
	}

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	public function createPreUpdateService($user)
	{
		return $this->createUpdateService($this->getAddUpdateRuleProcessor($user), $user);
	}

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	public function createPreDeleteService($user)
	{
		return $this->createDeleteService($this->getRuleProcessor($user), $user);
	}

	private function createAddService(RFReservationValidationRuleProcessor $ruleProcessor, $user)
	{
		$ruleProcessor->addRule(new RFReservationRuleAdminExcluded(new RFReservationRuleRequiresApproval(PluginManager::Instance()->loadAuthorization()), $userSession, $this->userRepository));
		return new RFReservationValidationServiceAdd($ruleProcessor);
	}

	private function createUpdateService(RFReservationValidationRuleProcessor $ruleProcessor, $user)
	{
		//if (Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_UPDATES_REQUIRE_APPROVAL, new BooleanConverter()))
		$reservationUpdateRequireApproval = false;
		if ($reservationUpdateRequireApproval)
		{
			$ruleProcessor->addRule(new RFReservationRuleAdminexcluded(new RFReservationRuleRequiresApproval(), $user, $this->userRepository));
			$ruleProcessor->addRule(new RFReservationRuleAdminexcluded(new RFReservationRuleUserIsOwner($user), $user, $this->userRepository));
		}
		return new RFReservationValidationServiceUpdate($ruleProcessor);
	}

	private function createDeleteService(RFReservationValidationRuleProcessor $ruleProcessor, $user)
	{
		$ruleProcessor->addRule(new RFReservationRuleAdminExcluded(new RFReservationRuleUserIsOwner($user), $user, $this->userRepository));
		return new RFReservationValidationServiceDelete($ruleProcessor);
	}

	private function getRuleProcessor($user)
	{
		// Common rules
		$rules = array();
		$rules[] = new RFReservationRuleReservationDatetime();
		//$rules[] = new RFReservationRuleInfo();
		$rules[] = new RFReservationRuleAdminexcluded(new RFReservationRuleReservationStarttime(), $user);
		//$rules[] = new AdminExcluded(new RFReservationRulePermissionValidationRule(new PermissionServiceFactory()), $user);
		$rules[] = new RFReservationRuleAdminExcluded(new RFReservationRuleResourceMinimumNotice(), $user);
		$rules[] = new RFReservationRuleAdminExcluded(new RFReservationRuleResourceMaximumNotice(), $user);
		//$rules[] = new RFReservationRuleAdminExcluded(new RFReservationRuleResourceParticipationRule(), $user);
		//$rules[] = new CustomAttributeValidationRule(new RFReservationRuleAttributeRepository());
		//$rules[] = new ReservationAttachmentRule();
		
		//$rules[] = new ReservationDateTimeRule();
		//$rules[] = new ReservationBasicInfoRule();
		//$rules[] = new AdminExcludedRule(new ReservationStartTimeRule($this->scheduleRepository), $userSession, $this->userRepository);
		//$rules[] = new AdminExcludedRule(new PermissionValidationRule(new PermissionServiceFactory()), $userSession, $this->userRepository);
		//$rules[] = new AdminExcludedRule(new ResourceMinimumNoticeRule(), $userSession, $this->userRepository);
		//$rules[] = new AdminExcludedRule(new ResourceMaximumNoticeRule(), $userSession, $this->userRepository);
		//$rules[] = new AdminExcludedRule(new ResourceParticipationRule(), $userSession, $this->userRepository);
		//$rules[] = new ReservationAttachmentRule();

		return new RFReservationValidationRuleProcessor($rules);
	}

	private function getAddUpdateRuleProcessor($user)
	{
		$config = JFactory::getConfig();
		$ruleProcessor = $this->getRuleProcessor($user);

		$ruleProcessor->addRule(new RFReservationRuleAdminexcluded(new RFReservationRuleResourceMinimumDuration($this->resourceRepository), $user, $this->userRepository));
		$ruleProcessor->addRule(new RFReservationRuleAdminexcluded(new RFReservationRuleResourceMaximumDuration($this->resourceRepository), $user, $this->userRepository));
		$ruleProcessor->addRule(new RFReservationRuleAdminexcluded(new RFReservationRuleResourceCrossday($this->scheduleRepository), $user, $this->userRepository));
		$ruleProcessor->addRule(new RFReservationRuleAdminexcluded(new RFReservationRuleQuota(new RFQuotaRepository(), $this->reservationRepository, $this->userRepository, $this->scheduleRepository), 
									$user, $this->userRepository)
								);
		$ruleProcessor->addRule(new RFReservationRuleSchedulePeriod($this->scheduleRepository, $user));
		//$ruleProcessor->addRule(new RFReservationRuleAdminexcluded(new CustomAttributeValidationRule(new AttributeService(new AttributeRepository())), $user, $this->userRepository));
		//$ruleProcessor->addRule(new AccessoryAvailabilityRule($this->reservationRepository, new AccessoryRepository(), $user->timezone));
		$ruleProcessor->addRule(new RFReservationRuleResourceAvailability(
									new RFResourceBlackoutAvailability($this->reservationRepository),
									$user->getParam('timezone', $config->get('config.offset')))
								);
		$ruleProcessor->addRule(new RFReservationRuleExistingResourceAvailability(
									new RFResourceReservationAvailability($this->reservationRepository), 
									$user->getParam('timezone', $config->get('config.offset')))
								);
		return $ruleProcessor;
	}
}

interface IPreReservationFactory
{
	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	public function createPreAddService($user);

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	*/
	public function createPreUpdateService($user);

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	*/
	public function createPreDeleteService($user);
}