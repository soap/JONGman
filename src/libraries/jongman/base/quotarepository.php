<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;


interface IQuotaRepository
{
	/**
	 * @abstract
	 * @return array|Quota[]
	 */
	function loadAll();

	/**
	 * @abstract
	 * @param Quota $quota
	 * @return void
	 */
	function add(RFQuota $quota);

	/**
	 * @abstract
	 * @param $quotaId
	 * @return void
	 */
	function deleteById($quotaId);
}

interface IQuotaViewRepository
{
	/**
	 * @abstract
	 * @return array|QuotaItem[]
	 */
	function getAll();
}