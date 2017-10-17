<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
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
