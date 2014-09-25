<?php
defined('_JEXEC') or die;

class RFResourceBookable
{
	protected $_resourceId;
	protected $_name;
	protected $_location;
	protected $_contact;
	protected $_notes;
	protected $_description;
	/**
	 * @var string|int
	 */
	protected $_minLength;
	/**
	 * @var string|int
	 */
	protected $_maxLength;
	protected $_autoAssign;
	protected $_requiresApproval;
	protected $_allowMultiday;
	protected $_maxParticipants;
	/**
	 * @var string|int
	 */
	protected $_minNotice;
	/**
	 * @var string|int
	 */
	protected $_maxNotice;
	protected $_scheduleId;
	protected $_imageName;
	protected $_isActive;
	protected $_adminGroupId;
	protected $_isCalendarSubscriptionAllowed = false;
	protected $_publicId;
	protected $_scheduleAdminGroupId;
	protected $_sortOrder;
	/**
	 * @var array|AttributeValue[]
	 */
	protected $_attributeValues = array();

	public function __construct($resourceId,
								$name,
								$location,
								$contact,
								$notes,
								$minLength,
								$maxLength,
								$autoAssign,
								$requiresApproval,
								$allowMultiday,
								$maxParticipants,
								$minNotice,
								$maxNotice,
								$description = null,
								$scheduleId = null,
								$adminGroupId = null
	)
	{
		$this->setResourceId($resourceId);
		$this->setName($name);
		$this->setLocation($location);
		$this->setContact($contact);
		$this->setNotes($notes);
		$this->setDescription($description);
		$this->setMinLength($minLength);
		$this->setMaxLength($maxLength);
		$this->setAutoAssign($autoAssign);
		$this->setRequiresApproval($requiresApproval);
		$this->setAllowMultiday($allowMultiday);
		$this->setMaxParticipants($maxParticipants);
		$this->setMinNotice($minNotice);
		$this->setMaxNotice($maxNotice);
		$this->setScheduleId($scheduleId);
		$this->setAdminGroupId($adminGroupId);
	}

	/**
	 * @param string $resourceName
	 * @param int $scheduleId
	 * @param bool $autoAssign
	 * @param int $order
	 * @return BookableResource
	 */
	public static function createNew($resourceName, $scheduleId, $autoAssign = false, $order = 0)
	{
		return new ResourceBookable(null,
			$resourceName,
			null,
			null,
			null,
			null,
			null,
			$autoAssign,
			null,
			null,
			null,
			null,
			null,
			null,
			$scheduleId);
	}

	/**
	 * @param array $row
	 * @return BookableResource
	 */
	public static function create($row)
	{
		if (isset($row->params) && $row->params != '') {
			$row->params = new JRegistry($row->params);
		}
		$resource = new RFResourceBookable($row->id,
			$row->title,
			$row->location,
			$row->contact_info,
			$row->note,
			$row->params->get('min_reservation_duration'),
			$row->params->get('max_reservation_duration'),
			$row->params->get('auto_assign'),
			$row->params->get('need_appoval'),
			$row->params->get('overlap_day_reservation'),
			$row->params->get('max_participants'),
			$row->params->get('min_notice_duration'),
			$row->params->get('max_notice_duration'),
			$row->description,
			$row->schedule_id);

		$resource->setImage($row->image);
		if (isset($row->admin_group_id)) {
			$resource->setAdminGroupId($row->admin_group_id);
		}
		$resource->setSortOrder($row->ordering);

		$resource->_isActive = true;
		if (isset($row->state))
		{
			$resource->_isActive = (bool)$row->state;
		}
		if (isset($row->public_id)) {
			$resource->withPublicId($row->public_id);
		}
		if (isset($row->allow_subscription)) {
			$resource->withSubscription($row->allow_subscription);
		}
		if (isset($row->schedule_admin_group_id)) {
			$resource->withScheduleAdminGroupId($row->schedule_admin_group_id);
		}
		
		return $resource;
	}

	public function getResourceId()
	{
		return $this->_resourceId;
	}

	public function getId()
	{
		return $this->_resourceId;
	}

	public function setResourceId($value)
	{
		$this->_resourceId = $value;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function setName($value)
	{
		$this->_name = $value;
	}

	public function getLocation()
	{
		return $this->_location;
	}

	public function setLocation($value)
	{
		$this->_location = $value;
	}

	public function hasLocation()
	{
		return !empty($this->_location);
	}

	public function getContact()
	{
		return $this->_contact;
	}

	public function SetContact($value)
	{
		$this->_contact = $value;
	}

	public function hasContact()
	{
		return !empty($this->_contact);
	}

	public function getNotes()
	{
		return $this->_notes;
	}

	public function setNotes($value)
	{
		$this->_notes = $value;
	}

	public function hasNotes()
	{
		return !empty($this->_notes);
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function setDescription($value)
	{
		$this->_description = $value;
	}

	public function hasDescription()
	{
		return !empty($this->_description);
	}

	/**
	 * @return TimeInterval
	 */
	public function getMinLength()
	{
		return TimeInterval::Parse($this->_minLength);
	}

	/**
	 * @param string|int $value
	 */
	public function setMinLength($value)
	{
		$this->_minLength = $value;
	}

	/**
	 * @return bool
	 */
	public function hasMinLength()
	{
		return !empty($this->_minLength);
	}

	/**
	 * @return TimeInterval
	 */
	public function getMaxLength()
	{
		return TimeInterval::Parse($this->_maxLength);
	}

	/**
	 * @param string|int $value
	 */
	public function setMaxLength($value)
	{
		$this->_maxLength = $value;
	}

	/**
	 * @return bool
	 */
	public function hasMaxLength()
	{
		return !empty($this->_maxLength);
	}

	/**
	 * @return bool
	 */
	public function getAutoAssign()
	{
		return $this->_autoAssign;
	}

	/**
	 * @param bool $value
	 * @return void
	 */
	public function setAutoAssign($value)
	{
		$this->_autoAssign = $value;
	}

	/**
	 * @return bool
	 */
	public function getRequiresApproval()
	{
		return $this->_requiresApproval;
	}

	/**
	 * @param bool $value
	 * @return void
	 */
	public function setRequiresApproval($value)
	{
		$this->_requiresApproval = $value;
	}

	/**
	 * @return bool
	 */
	public function getAllowMultiday()
	{
		return $this->_allowMultiday;
	}

	/**
	 * @param bool $value
	 * @return void
	 */
	public function setAllowMultiday($value)
	{
		$this->_allowMultiday = $value;
	}

	/**
	 * @return int
	 */
	public function getMaxParticipants()
	{
		return $this->_maxParticipants;
	}

	/**
	 * @param int $value
	 */
	public function setMaxParticipants($value)
	{
		$this->_maxParticipants = $value;
		if (empty($value))
		{
			$this->_maxParticipants = null;
		}
	}

	/**
	 * @return bool
	 */
	public function hasMaxParticipants()
	{
		return !empty($this->_maxParticipants);
	}

	/**
	 * @return TimeInterval
	 */
	public function getMinNotice()
	{
		return TimeInterval::Parse($this->_minNotice);
	}

	/**
	 * @param string|int $value
	 */
	public function setMinNotice($value)
	{
		$this->_minNotice = $value;
	}

	/**
	 * @return bool
	 */
	public function hasMinNotice()
	{
		return !empty($this->_minNotice);
	}

	/**
	 * @return TimeInterval
	 */
	public function getMaxNotice()
	{
		return TimeInterval::Parse($this->_maxNotice);
	}

	/**
	 * @param string|int $value
	 */
	public function setMaxNotice($value)
	{
		$this->_maxNotice = $value;
	}

	/**
	 * @return bool
	 */
	public function hasMaxNotice()
	{
		return !empty($this->_maxNotice);
	}

	/**
	 * @return int
	 */
	public function getScheduleId()
	{
		return $this->_scheduleId;
	}

	/**
	 * @param int $value
	 * @return void
	 */
	public function setScheduleId($value)
	{
		$this->_scheduleId = $value;
	}

	/**
	 * @return int
	 */
	public function getAdminGroupId()
	{
		return $this->_adminGroupId;
	}

	/**
	 * @param int $adminGroupId
	 */
	public function setAdminGroupId($adminGroupId)
	{
		$this->_adminGroupId = $adminGroupId;
		if (empty($adminGroupId))
		{
			$this->_adminGroupId = null;
		}
	}

	/**
	 * @return bool
	 */
	public function hasAdminGroup()
	{
		return !empty($this->_adminGroupId);
	}

	/**
	 * @return string
	 */
	public function getImage()
	{
		return $this->_imageName;
	}

	/**
	 * @param string $value
	 * @return void
	 */
	public function SetImage($value)
	{
		$this->_imageName = $value;
	}

	/**
	 * @return bool
	 */
	public function hasImage()
	{
		return !empty($this->_imageName);
	}

	/**
	 * @return bool
	 */
	public function isOnline()
	{
		return $this->_isActive;
	}

	/**
	 * @return void
	 */
	public function takeOffline()
	{
		$this->_isActive = false;
	}

	public function bringOnline()
	{
		$this->_isActive = true;
	}

	/**
	 * @param bool $isAllowed
	 */
	protected function setIsCalendarSubscriptionAllowed($isAllowed)
	{
		$this->_isCalendarSubscriptionAllowed = $isAllowed;
	}

	/**
	 * @return bool
	 */
	public function getIsCalendarSubscriptionAllowed()
	{
		return $this->_isCalendarSubscriptionAllowed;
	}

	/**
	 * @param string $publicId
	 */
	protected function setPublicId($publicId)
	{
		$this->_publicId = $publicId;
	}

	/**
	 * @return string
	 */
	public function getPublicId()
	{
		return $this->_publicId;
	}

	public function enableSubscription()
	{
		$this->setIsCalendarSubscriptionAllowed(true);
		if (empty($this->_publicId))
		{
			$this->setPublicId(uniqid());
		}
	}

	public function DisableSubscription()
	{
		$this->setIsCalendarSubscriptionAllowed(false);
	}

	public function withAttribute(AttributeValue $attribute)
	{
		$this->_attributeValues[$attribute->AttributeId] = $attribute;
	}

	/**
	 * @var array|AttributeValue[]
	 */
	private $_addedAttributeValues = array();

	/**
	 * @var array|AttributeValue[]
	 */
	private $_removedAttributeValues = array();

	/**
	 * @param $attributes AttributeValue[]|array
	 */
	public function changeAttributes($attributes)
	{
		$diff = new ArrayDiff($this->_attributeValues, $attributes);

		$added = $diff->getAddedToArray1();
		$removed = $diff->getRemovedFromArray1();

		/** @var $attribute AttributeValue */
		foreach ($added as $attribute)
		{
			$this->_addedAttributeValues[] = $attribute;
		}

		/** @var $accessory AttributeValue */
		foreach ($removed as $attribute)
		{
			$this->_removedAttributeValues[] = $attribute;
		}

		foreach ($attributes as $attribute)
		{
			$this->AddAttributeValue($attribute);
		}
	}

	/**
	 * @param $attributeValue AttributeValue
	 */
	public function addAttributeValue($attributeValue)
	{
		$this->_attributeValues[$attributeValue->AttributeId] = $attributeValue;
	}

	/**
	 * @return array|AttributeValue[]
	 */
	public function getAddedAttributes()
	{
		return $this->_addedAttributeValues;
	}

	/**
	 * @return array|AttributeValue[]
	 */
	public function getRemovedAttributes()
	{
		return $this->_removedAttributeValues;
	}

	/**
	 * @param $customAttributeId
	 * @return mixed
	 */
	public function getAttributeValue($customAttributeId)
	{
		if (array_key_exists($customAttributeId, $this->_attributeValues))
		{
			return $this->_attributeValues[$customAttributeId]->Value;
		}

		return null;
	}

	/**
	 * @return string
	 */
	public function __toString()
	{
		return 'BookableResource' . $this->_resourceId;
	}

	/**
	 * @static
	 * @return BookableResource
	 */
	public static function Null()
	{
		return new RFResourceBookable(null, null, null, null, null, null, null, false, false, false, null, null, null);
	}

	protected function withPublicId($publicId)
	{
		$this->setPublicId($publicId);
	}

	protected function withSubscription($isAllowed)
	{
		$this->setIsCalendarSubscriptionAllowed($isAllowed);
	}

	/**
	 * @param $scheduleAdminGroupId int
	 */
	protected function withScheduleAdminGroupId($scheduleAdminGroupId)
	{
		$this->_scheduleAdminGroupId = $scheduleAdminGroupId;
	}

	/**
	 * @return int
	 */
	public function getScheduleAdminGroupId()
	{
		return $this->_scheduleAdminGroupId;
	}

	/**
	 * @param int $sortOrder
	 */
	public function setSortOrder($sortOrder)
	{
		$this->_sortOrder = intval($sortOrder);
	}

	/**
	 * @return int
	 */
	public function getSortOrder()
	{
		return $this->_sortOrder;
	}
}