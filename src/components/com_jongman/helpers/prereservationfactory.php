<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
jimport('jongman.base.iprereservationfactory');

class PreReservationFactory implements IPreReservationFactory
{
	/**
	 * @var ResourceRepository
	 * provide resources by schedule or all resources as well as CRUD operation
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
		$this->resourceRepository = new ResourceRepository();
		$this->reservationRepository = new ReservationViewRepository();
		$this->scheduleRepository = new ScheduleRepository();
		$this->userRepository = new UserRepository();
	}

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	public function createPreAddService(JUser $user)
	{
		return $this->createAddService($this->getAddUpdateRuleProcessor($userSession), $userSession);
	}

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	public function createPreUpdateService(UserSession $userSession)
	{
		return $this->createUpdateService($this->getAddUpdateRuleProcessor($userSession), $userSession);
	}

	/**
	 * @param UserSession $userSession
	 * @return IReservationValidationService
	 */
	public function createPreDeleteService(JUser $user)
	{
		return $this->createDeleteService($this->getRuleProcessor($userSession), $user);
	}

	private function createAddService(RFReservationValidationRuleprocessor $ruleProcessor, JUser $user)
	{
		$ruleProcessor->addRule(
					new RFValidationRuleAdminExcluded(new RFValidationRuleRequiresapproval(PluginManager::Instance()->LoadAuthorization()), $user));
		return new addReservationValidationService($ruleProcessor);
	}

	private function createUpdateService(ReservationValidationRuleProcessor $ruleProcessor, JUser $user)
	{
		if (Configuration::Instance()->GetSectionKey(ConfigSection::RESERVATION, ConfigKeys::RESERVATION_UPDATES_REQUIRE_APPROVAL, new BooleanConverter()))
		{
			$ruleProcessor->addRule(new AdminExcludedRule(new RequiresApprovalRule(PluginManager::Instance()->LoadAuthorization()), $userSession));
		}
		return new UpdateReservationValidationService($ruleProcessor);
	}

	private function createDeleteService(RFReservationValidationRuleProcessor $ruleProcessor, JUser $user)
	{
		return new DeleteReservationValidationService($ruleProcessor);
	}

	private function getRuleProcessor(JUser $user)
	{
		// Common rules
		$rules = array();
		$rules[] = new RFValidationRuleReservationDatetime();
		$rules[] = new RFValidationRuleAdminExcluded(new RFValidationRuleReservationStartTimeRule($this->scheduleRepository), $user);
		//$rules[] = new RFValidationRuleAdminExcluded(new RFValidationRulePermissionValidationRule(new PermissionServiceFactory()), $user);
		$rules[] = new RFValidationRuleAdminExcluded(new RFValidationRuleResourceMinimumNoticeRule(), $user);
		$rules[] = new RFValidationRuleAdminExcluded(new RFValidationRuleResourceMaximumNoticeRule(), $user);
		//$rules[] = new RFValidationRuleAdminExcluded(new RFValidationRuleResourceParticipationRule(), $user);
		//$rules[] = new CustomAttributeValidationRule(new RFValidationRuleAttributeRepository());
		//$rules[] = new ReservationAttachmentRule();

		return new RFReservationValidationRuleProcessor($rules);
	}

	private function getAddUpdateRuleProcessor(JUser $user)
	{
		/* Get common rules */
		$ruleProcessor = $this->getRuleProcessor($user);

		$ruleProcessor->addRule(new ExistingResourceAvailabilityRule(new ResourceReservationAvailability($this->reservationRepository), $user->timezone));
		//$ruleProcessor->AddRule(new AccessoryAvailabilityRule($this->reservationRepository, new AccessoryRepository(), $user->Timezone));
		$ruleProcessor->addRule(new RFValidationRuleResourceAvailable(new RFValidationRuleResourceBlackoutAvailable($this->reservationRepository), $user->timezone));
		$ruleProcessor->addRule(new RFValidationRuleAdminExcluded(new RFValidationRuleResourceMinimumDuration($this->resourceRepository), $user));
		$ruleProcessor->addRule(new RFValidationRuleAdminExcluded(new RFValidationRuleResourceMaximumDuration($this->resourceRepository), $user));
		//$ruleProcessor->addRule(new RFValidationRuleAdminExcluded(new QuotaRule(new QuotaRepository(), $this->reservationRepository, $this->userRepository, $this->scheduleRepository), $userSession));
		$ruleProcessor->addRule(new RFValidationRuleSchedulePeriod($this->scheduleRepository, $userSession));

		return $ruleProcessor;
	}
}