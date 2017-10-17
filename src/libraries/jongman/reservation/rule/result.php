<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFReservationRuleResult
{
	private $_isValid;
	private $_errorMessage;
	
	/**
	 * @param bool $isValid
	 * @param string $errorMessage
	 */
	public function __construct($isValid = true, $errorMessage = null)
	{
		$this->_isValid = $isValid;
		$this->_errorMessage = $errorMessage;
	}
	
	/**
	 * @return bool
	 */
	public function isValid()
	{
		return $this->_isValid;
	}
	
	/**
	 * @return string
	 */
	public function errorMessage()
	{
		return $this->_errorMessage;
	}
}