<?php
defined('_JEXEC') or die;

class RFQuotaItem //View
{
	public $id;
	public $limit;
	public $unit;
	public $duration;
	public $groupName;
	public $resourceName;
	public $scheduleName;

	/**
	 * @param int $quotaId
	 * @param decimal $limit
	 * @param string $unit
	 * @param string $duration
	 * @param string $groupName
	 * @param string $resourceName
	 * @param string $scheduleName
	 */
	public function __construct($quotaId, $limit, $unit, $duration, $groupName, $resourceName, $scheduleName)
	{
		$this->id = $quotaId;
		$this->limit = $limit;
		$this->unit = $unit;
		$this->duration = $duration;
		$this->groupName = $groupName;
		$this->resourceName = $resourceName;
		$this->scheduleName = $scheduleName;
	}
}