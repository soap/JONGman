<?php
defined('_JEXEC') or die;

abstract class RFReservationRepeatAbstract implements IRepeatOptions
{
	/**
	 * @var int
	 */
	protected $_interval;

	/**
	 * @var Date
	 */
	protected $_terminationDate;

	/**
	 * @return Date
	 */
	public function terminationDate()
	{
		return $this->_terminationDate;
	}

	/**
	 * @param int $interval
	 * @param Date $terminationDate
	 */
	protected function __construct($interval, $terminationDate)
	{
		$this->_interval = $interval;
		$this->_terminationDate = $terminationDate;
	}

	public function configurationString()
	{
		$obj = new JRegistry();
		$obj->set('interval', $this->_interval);
		$obj->set('termination', $this->_terminationDate->toDatabase());
		
		return $obj->toString();
	}

	public function equals(IRepeatOptions $repeatOptions)
	{
		return $this->configurationString() == $repeatOptions->configurationString();
	}

	public function hasSameConfigurationAs(IRepeatOptions $repeatOptions)
	{
		return get_class($this) == get_class($repeatOptions) && $this->_interval == $repeatOptions->_interval;
	}
}
