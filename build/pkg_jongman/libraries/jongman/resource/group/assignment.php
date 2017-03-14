<?php
defined('_JEXEC') or die;
class ResourceGroupAssignment implements IResource
{
	public $type = RFResourceGroup::RESOURCE_TYPE;
	public $group_id;
	public $resource_name;
	public $id;
	public $label;
	public $resource_id;

	private $resourceAdminGroupId;
	private $scheduleId;
	private $statusId;
	/**
	 * @var
	 */
	private $scheduleAdminGroupId;

	public function __construct($group_id, $resource_name, $resource_id, $resourceAdminGroupId, $scheduleId, $statusId, $scheduleAdminGroupId)
	{
		$this->group_id = $group_id;
		$this->resource_name = $resource_name;
		$this->id = "{$this->type}-{$group_id}-{$resource_id}";
		$this->label = $resource_name;
		$this->resource_id = $resource_id;
		$this->resourceAdminGroupId = $resourceAdminGroupId;
		$this->scheduleId = $scheduleId;
		$this->statusId = $statusId;
		$this->scheduleAdminGroupId = $scheduleAdminGroupId;
	}

	public function getId()
	{
		return $this->resource_id;
	}

	public function getName()
	{
		return $this->resource_name;
	}

	public function getAdminGroupId()
	{
		return $this->resourceAdminGroupId;
	}

	public function getScheduleId()
	{
		return $this->scheduleId;
	}

	public function getScheduleAdminGroupId()
	{
		return $this->scheduleAdminGroupId;
	}

	public function getStatusId()
	{
		return $this->statusId;
	}

	public function getResourceId()
	{
		return $this->resource_id;
	}
}