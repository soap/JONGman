<?php
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