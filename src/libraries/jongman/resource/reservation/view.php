<?php
defined('_JEXEC') or die;
jimport('jongman.base.iresource');

class RFResourceReservationView implements IResource
{
	private $_id;
	private $_resourceName;
	private $_adminGroupId;
	private $_scheduleId;
	private $_scheduleAdminGroupId;
	private $_statusId;

	public function __construct($resourceId, $resourceName, $adminGroupId, $scheduleId, $scheduleAdminGroupId, $statusId = RFResourceStatus::AVAILABLE)
	{
		$this->_id = $resourceId;
		$this->_resourceName = $resourceName;
		$this->_adminGroupId = $adminGroupId;
		$this->_scheduleId = $scheduleId;
		$this->_scheduleAdminGroupId = $scheduleAdminGroupId;
		$this->_statusId = $statusId;
	}

	/**
	 * @return int
	 */
	public function id()
	{
		return $this->_id;
	}

	/**
	 * @return string
	 */
	public function name()
	{
		return $this->_resourceName;
	}

	/**
	 * @return int|null
	 */
	public function getAdminGroupId()
	{
		return $this->_adminGroupId;
	}

	/**
	 * alias of GetId()
	 * @return int
	 */
	public function getResourceId()
	{
		return $this->Id();
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->Id();
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name();
	}

	/**
	 * @return int
	 */
	public function getScheduleId()
	{
		return $this->_scheduleId;
	}

	/**
	 * @return int
	 */
	public function getScheduleAdminGroupId()
	{
		return $this->_scheduleAdminGroupId;
	}

	/**
	 * @return int
	 */
	public function getStatusId()
	{
		return $this->_statusId;
	}
}
