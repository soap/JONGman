<?php
defined('_JEXEC') or die;

interface IScheduleResourceFilter
{
	/**
	 * @param BookableResource[] $resources
	 * @param IResourceRepository $resourceRepository
	 * @param IAttributeService $attributeService
	 * @return int[] filtered resource ids
	 */
	public function filterResources($resources, IResourceRepository $resourceRepository,
			IAttributeService $attributeService);
}

class RFScheduleResourceFilter implements IScheduleResourceFilter
{
	public $scheduleId;
	public $resourceId;
	public $groupId;
	public $resourceTypeId;
	public $minCapacity;
	public $resourceAttributes;
	public $resourceTypeAttributes;

	/**
	 * @param int|null $scheduleId
	 * @param int|null $resourceTypeId
	 * @param int|null $minCapacity
	 * @param AttributeValue[]|null $resourceAttributes
	 * @param AttributeValue[]|null $resourceTypeAttributes
	 */
	public function __construct($scheduleId = null,
			$resourceTypeId = null,
			$minCapacity = null,
			$resourceAttributes = null,
			$resourceTypeAttributes = null)
	{
		$this->scheduleId = $scheduleId;
		$this->resourceTypeId = $resourceTypeId;
		$this->minCapacity = empty($minCapacity) ? null : $minCapacity;
		$this->resourceAttributes = empty($resourceAttributes) ? array() : $resourceAttributes;
		$this->resourceTypeAttributes = empty($resourceTypeAttributes) ? array() : $resourceTypeAttributes;
	}

	public static function fromCookie($val)
	{
		if (empty($val))
		{
			return new RFScheduleResourceFilter();
		}
		return new RFScheduleResourceFilter($val->scheduleId, $val->resourceTypeId, $val->minCapacity, $val->resourceAttributes, $val->resourceTypeAttributes);
	}

	private function hasFilter()
	{
		return !empty($this->resourceId) || !empty($this->groupId) || !empty($this->resourceTypeId) || !empty($this->minCapacity) || !empty($this->resourceAttributes) || !empty($this->resourceTypeAttributes);
	}

	public function filterResources($resources, IResourceRepository $resourceRepository,
			IAttributeService $attributeService)
	{
		$resourceIds = array();

		if (!$this->hasFilter())
		{
			foreach ($resources as $resource)
			{
				$resourceIds[] = $resource->getId();
			}

			return $resourceIds;
		}

		$groupResourceIds = array();
		if (!empty($this->groupId) && empty($this->resourceId))
		{
			$groups = $resourceRepository->getResourceGroups($this->scheduleId);
			$groupResourceIds = $groups->getResourceIds($this->groupId);
		}

		$resourceAttributeValues = null;
		if (!empty($this->resourceAttributes))
		{
			$resourceAttributeValues = $attributeService->getAttributes(RFCustomAttributeCategory::RESOURCE, null);
		}

		$resourceTypeAttributeValues = null;
		if (!empty($this->resourceTypeAttributes))
		{
			$resourceTypeAttributeValues = $attributeService->getAttributes(RFCustomAttributeCategory::RESOURCE_TYPE, null);
		}

		$resourceIds = array();

		foreach ($resources as $resource)
		{
			$resourceIds[] = $resource->getId();

			if (!empty($this->resourceId) && $resource->getId() != $this->resourceId)
			{
				array_pop($resourceIds);
				continue;
			}

			if (!empty($this->groupId) && !in_array($resource->getId(), $groupResourceIds))
			{
				array_pop($resourceIds);
				continue;
			}

			if (!empty($this->minCapacity) && $resource->getMaxParticipants() < $this->minCapacity)
			{
				array_pop($resourceIds);
				continue;
			}

			if (!empty($this->resourceTypeId) && $resource->getResourceTypeId() != $this->resourceTypeId)
			{
				array_pop($resourceIds);
				continue;
			}

			$resourceAttributesPass = true;
			if (!empty($this->resourceAttributes))
			{
				$values = $resourceAttributeValues->getAttributes($resource->getId());

				/** var @attribute AttributeValue */
				foreach ($this->resourceAttributes as $attribute)
				{
					$value = $this->getAttribute($values, $attribute->attributeId);
					if (!$this->attributeValueMatches($attribute, $value))
					{
						$resourceAttributesPass = false;
						break;
					}
				}
			}

			if (!$resourceAttributesPass)
			{
				array_pop($resourceIds);
				continue;
			}

			$resourceTypeAttributesPass = true;

			if (!empty($this->resourceTypeAttributes))
			{
				if (!$resource->hasResourceType())
				{
					array_pop($resourceIds);
					// there's a filter but this resource doesn't have a resource type
					continue;
				}
				$values = $resourceTypeAttributeValues->getAttributes($resource->getResourceTypeId());

				/** var @attribute AttributeValue */
				foreach ($this->resourceTypeAttributes as $attribute)
				{
					$value = $this->getAttribute($values, $attribute->attributeId);
					if (!$this->attributeValueMatches($attribute, $value))
					{
						$resourceTypeAttributesPass = false;
						break;
					}
				}
			}

			if (!$resourceTypeAttributesPass)
			{
				array_pop($resourceIds);
				continue;
			}

		}

		return $resourceIds;
	}

	/**
	 * @param Attribute[] $attributes
	 * @param int $attributeId
	 * @return null|Attribute
	 */
	private function getAttribute($attributes, $attributeId)
	{
		foreach ($attributes as $attribute)
		{
			if ($attribute->Id() == $attributeId)
			{
				return $attribute;
			}
		}
		return null;
	}

	/**
	 * @param AttributeValue $attribute
	 * @param Attribute $value
	 * @return bool
	 */
	private function attributeValueMatches($attribute, $value)
	{
		if ($value == null)
		{
			return false;
		}

		if ($value->type() == RFCustomAttributeTypes::SINGLE_LINE_TEXTBOX || $value->type() == RFCustomAttributeTypes::MULTI_LINE_TEXTBOX)
		{
			return strripos($value->Value(), $attribute->Value) !== false;
		}
		else
		{
			return $value->value() == $attribute->value;
		}
	}
}