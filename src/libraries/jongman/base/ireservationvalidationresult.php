<?php
defined('_JEXEC') or die;


interface IReservationValidationResult
{
	/**
	 * @return bool
	 */
	public function canBeSaved();

	/**
	 * @return array[int]string
	*/
	public function getErrors();

	/**
	 * @return array[int]string
	*/
	public function getWarnings();
}