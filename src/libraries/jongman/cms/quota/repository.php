<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('jongman.base.quotarepository');

class RFQuotaRepository implements IQuotaRepository, IQuotaViewRepository
{
	public function loadAll()
	{
		$config = array('ignore_request'=>true);
		$model = JModelLegacy::getInstance('Quotas', 'JongmanModel', $config);
		$model->setState('filter.published', '1');
		$items = $model->getItems();
			
		$quotas = array();
		foreach($items as $item)
		{
			$quotaId = $item->id;

			$limit = RFQuota::createLimit($item->quota_limit, $item->unit);
			$duration = RFQuota::createDuration($item->duration);

			$resourceId = $item->resource_id;
			$groupId = $item->group_id;
			$scheduleId = $item->schedule_id;
			
			$quotas[] = new RFQuota($quotaId, $duration, $limit, $resourceId, $groupId, $scheduleId);
		}

		return $quotas;		
	}
	
	public function add(RFQuota $quota)
	{
		
	}
	
	public function getAll()
	{
		
	}
	
	public function deleteById($quotaId)
	{
		
	}
}