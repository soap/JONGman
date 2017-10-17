<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFReservationRuleResourceCrossday implements IReservationValidationRule
{
	/**
	 * @var IScheduleRepository
	 */
	private $scheduleRepository;

	public function __construct(IScheduleRepository $scheduleRepository)
	{
		$this->scheduleRepository = $scheduleRepository;
	}

	public function validate($reservationSeries)
	{
		foreach ($reservationSeries->allResources() as $resource)
		{
			if (!$resource->getAllowMultiday())
			{
				$schedule = $this->scheduleRepository->loadById($reservationSeries->scheduleId());
				$tz = $schedule->getTimezone();
				$isSameDay = $reservationSeries->currentInstance()->startDate()->toTimezone($tz)->dateEquals($reservationSeries->currentInstance()->endDate()->toTimezone($tz));

				return new RFReservationRuleResult($isSameDay, JText::sprintf('COM_JONGMAN_VALIDATION_CROSSDAY_RULE', $resource->getName()) );
			}
		}

		return new RFReservationRuleResult();
	}
	
	public function getError()
	{
		
	}
}