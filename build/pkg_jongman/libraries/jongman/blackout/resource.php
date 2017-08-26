<?php
defined('_JEXEC') or die;
jimport('jongman.base.iresource');

class RFBlackoutResource implements IResource
{
	private $id;
	private $name;
	private $scheduleId;
	private $adminGroupId;
	private $scheduleAdminGroupId;
	private $statusId;

	public function __construct($id, $name, $scheduleId, $adminGroupId = null, $scheduleAdminGroupId = null, $statusId = null)
	{
		$this->id = $id;
		$this->name = $name;
		$this->scheduleId = $scheduleId;
		$this->adminGroupId = $adminGroupId;
		$this->scheduleAdminGroupId = $scheduleAdminGroupId;
		$this->statusId = $statusId;
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return int
	 */
	public function getAdminGroupId()
	{
		return $this->adminGroupId;
	}

	/**
	 * @return int
	 */
	public function getScheduleId()
	{
		return $this->scheduleId;
	}

	/**
	 * @return int
	 */
	public function getScheduleAdminGroupId()
	{
		return $this->scheduleAdminGroupId;
	}

	/**
	 * @return int
	 */
	public function getResourceId()
	{
		return $this->id;
	}


	/**
	 * @return int
	 */
	public function getStatusId()
	{
		return $this->statusId;
	}
}