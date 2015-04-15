<?php
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

				return new RFReservationRuleResult($isSameDay, JText::printf('COM_JONGMAN_VALIDATION_MULTIDAY_RULE', $resource->getName()) );
			}
		}

		return new RFReservationRuleResult();
	}
	
	public function getError()
	{
		
	}
}