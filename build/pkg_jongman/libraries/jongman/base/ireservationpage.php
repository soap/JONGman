<?php
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