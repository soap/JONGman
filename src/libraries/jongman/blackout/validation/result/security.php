<?php
defined('_JEXEC') or die;
jimport('jongman.base.iblackoutvalidationresult');

class RFBlackoutValidationResultSecurity implements IBlackoutValidationResult
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
		return JText::_('COM_JONGMAN_BLACKOUT_VALIDATION_RESULT_NO_PERRMISSION');
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