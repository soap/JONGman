<?php
defined('_JEXEC') or die;

class RFReservationValidationRuleProcessor //implements IReservationValidationService
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
			$result = $rule->validate($reservationSeries);
			JLog::add(JText::sprintf("COM_JONGMAN_LOG_VALIDATION_RULE_PASSED", get_class($rule), $result->isValid()), JLog::INFO);

			if (!$result->isValid())
			{
				$errors = $rule->getError();
				if (!is_array($errors)) $errors = array($errors);
				return new RFReservationValidationResult(false, $errors);
			}
		}

		return new RFReservationValidationResult();
	}

	public function addRule($validationRule)
	{
		$this->_validationRules[] = $validationRule;
	}
}