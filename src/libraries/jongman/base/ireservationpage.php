<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IReservationPage
{
	public function getReferenceNumber();
	public function getSeriesUpdateScope();
	
	/**
	 * @return int
	 */
	public function getUserId();
	
	/**
	 * @return int
	 */
	public function getCustomerId();

	/**
	 * @return int
	*/
	public function getResourceId();
	
	/**
	 * @return string
	*/
	public function getTitle();
	
	/**
	 * @return string
	*/
	public function getDescription();
	
	/**
	 * @return string
	*/
	public function getStartDate();
	
	/**
	 * @return string
	*/
	public function getEndDate();
	
	/**
	 * @return string
	*/
	public function getStartTime();
	
	/**
	 * @return string
	*/
	public function getEndTime();
	
	/**
	 * @return int[]
	*/
	public function getResources();
}