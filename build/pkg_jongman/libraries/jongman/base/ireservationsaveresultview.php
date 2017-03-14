<?php
defined ('_JEXEC') or die;

/** 
 * Interface to set error/warning message to display in view 
 * after processing reservation data 
 * 
 * for Joomla implemented by Reservation and Instance Model
 **/
interface IReservationSaveResultView
{
	/**
	 * @param bool $succeeded
	 */
	public function setSaveSuccessfulMessage($succeeded);

	/**
	 * @param array|string[] $errors
	 */
	public function setErrors($errors);

	/**
	 * @param array|string[] $warnings
	 */
	public function setWarnings($warnings);
	
}