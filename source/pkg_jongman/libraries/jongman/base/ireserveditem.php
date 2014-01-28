<?php
defined('_JEXEC') or die;

interface IReservedItem
{
	/**
	 * @abstract
	 * @return Date
	 */
	public function getStartDate();

	/**
	 * @abstract
	 * @return Date
	 */
	public function getEndDate();

	/**
	 * @abstract
	 * @return int
	 */
	public function getResourceId();

	/**
	 * @abstract
	 * @return int
	 */
	public function getId();

	/**
	 * @abstract
	 * @param Date $date
	 * @return bool
	 */
	public function occursOn(JMDate $date);
}
