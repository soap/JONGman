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
			$row->auto_assign,
			$row->params->get('need_appoval'),
			$row->allow_multi,
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

	public function SetResourceId($value)
	{
		$this->_resourceId = $value;
	}

	public function getName()
	{
		return $this->_name;
	}

	public function SetName($value)
	{
		$this->_name = $value;
	}

	public function getLocation()
	{
		return $this->_location;
	}

	public function SetLocation($value)
	{
		$this->_location = $value;
	}

	public function HasLocation()
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

	public function HasContact()
	{
		return !empty($this->_contact);
	}

	public function getNotes()
	{
		return $this->_notes;
	}

	public function SetNotes($value)
	{
		$this->_notes = $value;
	}

	public function HasNotes()
	{
		return !empty($this->_notes);
	}

	public function getDescription()
	{
		return $this->_description;
	}

	public function SetDescription($value)
	{
		$this->_description = $value;
	}

	public function HasDescription()
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
	public function SetMinLength($value)
	{
		$this->_minLength = $value;
	}

	/**
	 * @return bool
	 */
	public function HasMinLength()
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
	public function SetMaxLength($value)
	{
		$this->_maxLength = $value;
	}

	/**
	 * @return bool
	 */
	public function HasMaxLength()
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
	public function SetAutoAssign($value)
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
	public function SetRequiresApproval($value)
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
	public function SetAllowMultiday($value)
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
	public function SetMaxParticipants($value)
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
	public function HasMaxParticipants()
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
	public function SetMinNotice($value)
	{
		$this->_minNotice = $value;
	}

	/**
	 * @return bool
	 */
	public function HasMinNotice()
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
	public function SetMaxNotice($value)
	{
		$this->_maxNotice = $value;
	}

	/**
	 * @return bool
	 */
	public function HasMaxNotice()
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
	public function SetScheduleId($value)
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
	public function SetAdminGroupId($adminGroupId)
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
	public function HasAdminGroup()
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
	public function HasImage()
	{
		return !empty($this->_imageName);
	}

	/**
	 * @return bool
	 */
	public function IsOnline()
	{
		return $this->_isActive;
	}

	/**
	 * @return void
	 */
	public function TakeOffline()
	{
		$this->_isActive = false;
	}

	public function BringOnline()
	{
		$this->_isActive = true;
	}

	/**
	 * @param bool $isAllowed
	 */
	protected function SetIsCalendarSubscriptionAllowed($isAllowed)
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
	protected function SetPublicId($publicId)
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

	public function EnableSubscription()
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

	public function WithAttribute(AttributeValue $attribute)
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
	public function ChangeAttributes($attributes)
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
	public function AddAttributeValue($attributeValue)
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
		return new BookableResource(null, null, null, null, null, null, null, false, false, false, null, null, null);
	}

	protected function WithPublicId($publicId)
	{
		$this->setPublicId($publicId);
	}

	protected function WithSubscription($isAllowed)
	{
		$this->setIsCalendarSubscriptionAllowed($isAllowed);
	}

	/**
	 * @param $scheduleAdminGroupId int
	 */
	protected function WithScheduleAdminGroupId($scheduleAdminGroupId)
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
	public function SetSortOrder($sortOrder)
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