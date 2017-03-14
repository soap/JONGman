<?php
defined('_JEXEC') or die;

class RFSchedulePeriodSpanable extends RFSchedulePeriod
{
	private $span = 1;
	private $period;

	public function __construct(RFSchedulePeriod $period, $span = 1)
	{
		$this->span = $span;
		$this->period = $period;
		parent::__construct($period->beginDate(), $period->endDate(), $period->_label);

	}

	public function span()
	{
		return $this->span;
	}

	public function setSpan($span)
	{
		$this->span = $span;
	}

	public function isReservable()
	{
		return $this->period->isReservable();
	}
}