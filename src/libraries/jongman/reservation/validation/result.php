<?php
defined('_JEXEC') or die;

class RFReservationValidationResult implements IReservationValidationResult
{
	private $_canBeSaved;
	private $_errors;
	private $_warnings;

	/**
	 * @param $canBeSaved bool
	 * @param $errors string[]
	 * @param $warnings string[]
	 */
	public function __construct($canBeSaved = true, $errors = null, $warnings = null)
	{
		$this->_canBeSaved = $canBeSaved;
		$this->_errors = $errors == null ? array() : $errors;
		$this->_warnings = $warnings == null ? array() : $warnings;
	}

	public function canBeSaved()
	{
		return $this->_canBeSaved;
	}

	public function getErrors()
	{
		return $this->_errors;
	}

	public function getWarnings()
	{
		return $this->_warnings;
	}
}