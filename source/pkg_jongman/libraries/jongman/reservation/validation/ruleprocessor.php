<?php
defined('_JEXEC') or die;


class RFReservationValidationRuleprocessor implements IReservationValidationService
{
	/**
	 * @var array|IReservationValidationRule[]
	 */
	private $_validationRules = array();

	public function __construct($validationRules)
	{
		$this->_validationRules = $validationRules;
	}

	public function validate($reservationSeries)
	{
		/** @var $rule IReservationValidationRule */
		foreach ($this->_validationRules as $rule)
		{
			$passed = $rule->validate($reservationSeries);
			//Log::Debug('Validating rule %s. Passed?: %s', get_class($rule), $passed . '');

			if (!$passed)
			{
				return new RFReservationValidationResult(false, array($rule->getError()));
			}
		}

		return new RFReservationValidationResult();
	}

	public function addRule($validationRule)
	{
		$this->_validationRules[] = $validationRule;
	}
}