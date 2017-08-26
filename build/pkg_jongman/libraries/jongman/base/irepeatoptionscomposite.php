<?php

interface IRepeatOptionsComposite
{
	/**
	 * @abstract
	 * @return string
	 */
	public function getRepeatType();

	/**
	 * @abstract
	 * @return string|null
	*/
	public function getRepeatInterval();

	/**
	 * @abstract
	 * @return int[]|null
	*/
	public function getRepeatWeekdays();

	/**
	 * @abstract
	 * @return string|null
	*/
	public function getRepeatMonthlyType();

	/**
	 * @abstract
	 * @return string|null
	*/
	public function getRepeatTerminationDate();
}