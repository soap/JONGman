<?php
defined('_JEXEC') or die;
interface IPermissibleResource
{
	/**
	 * @abstract
	 * @return int
	 */
	public function getResourceId();
}

interface IResource extends IPermissibleResource
{
	/**
	 * @return int
	 */
	public function getId();

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return int
	 */
	public function getAdminGroupId();

	/**
	 * @return int
	 */
	public function getScheduleId();

	/**
	 * @abstract
	 * @return int
	 */
	public function getScheduleAdminGroupId();
}
