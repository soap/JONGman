<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
jimport('jongman.base.iblackoutvalidationresult');

class RFBlackoutValidationResultDatetime implements IBlackoutValidationResult
{
	/**
	 * @return bool
	 */
	public function wasSuccessful()
	{
		return false;
	}

	/**
	 * @return string
	 */
	public function message()
	{
		return JText::_('COM_JONGMAN_RULE_VIOLATION_STARTDATE_BEFORE_ENDDATE');
	}

	/**
	 * @return array|RFReservationItemView[]
	 */
	public function conflictingReservations()
	{
		return array();
	}

	/**
	 * @return array|RFBlackoutItemView[]
	 */
	public function conflictingBlackouts()
	{
		return array();
	}
}