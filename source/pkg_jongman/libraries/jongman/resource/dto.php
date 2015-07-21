<?php
defined('_JEXEC') or die;
class ResourceDto
{
	/**
	 * @param int $id
	 * @param string $name
	 * @param bool $canAccess
	 * @param null|int $scheduleId
	 * @param null|RFTimeInterval $minLength
	 */
	public function __construct($id, $name, $canAccess = true, $scheduleId = null, $minLength = null)
	{
		$this->id = $id;
		$this->name = $name;
		$this->canAccess = $canAccess;
		$this->scheduleId = $scheduleId;
		$this->minimumLength = $minLength;
	}

	/**
	 * @var int
	 */
	public $id;

	/**
	 * @var string
	 */
	public $name;

	/**
	 * @var bool
	 */
	public $canAccess;

	/**
	 * @var null|int
	 */
	public $scheduleId;

	/**
	 * @var null|TimeInterval
	 */
	public $minimumLength;

	/**
	 * alias of getId()
	 * @return int
	 */
	public function getResourceId()
	{
		return $this->id;
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
	 * @return int|null
	 */
	public function GetScheduleId()
	{
		return $this->scheduleId;
	}

	/**
	 * @return null|RFTimeInterval
	 */
	public function getMinimumLength()
	{
		return $this->minimumLength;
	}
}