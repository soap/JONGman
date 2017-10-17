<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


class RFReservationValidationRuleprocessor //implements IReservationValidationService
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
			JLog::add(JText::sprintf("COM_JONGMAN_LOG_VALIDATION_RULE_PASSED", get_class($rule), $passed->canBeSaved()), JLog::INFO);

			if (!$passed->canBeSaved())
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