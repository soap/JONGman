<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

interface IResourceService
{
	/**
	 * Gets resource list for a schedule
	 * @param int $scheduleId
	 * @param bool $includeInaccessibleResources
	 * @param UserSession $user
	 * @param ScheduleResourceFilter|null $filter
	 * @return array|ResourceDto[]
	 */
	public function getScheduleResources($scheduleId, $includeInaccessibleResources, JUser $user, $filter = null);

	/**
	 * Gets resource list
	 * @param bool $includeInaccessibleResources
	 * @param UserSession $user
	 * @return array|ResourceDto[]
	*/
	public function getAllResources($includeInaccessibleResources, JUser $user);

	/**
	 * @abstract
	 * @return array|AccessoryDto[]
	*/
	public function getAccessories();

	/**
	 * @param int $scheduleId
	 * @param UserSession $user
	 * @return ResourceGroupTree
	*/
	public function getResourceGroups($scheduleId, JUser $user);

	/**
	 * @return ResourceType[]
	*/
	public function getResourceTypes();

	/**
	 * @return Attribute[]
	*/
	public function getResourceAttributes();

	/**
	 * @return Attribute[]
	*/
	public function getResourceTypeAttributes();
}

class RFResourceService implements IResourceService
{
	/**
	 * @var IResourceRepository
	 */
	private $_resourceRepository;

	/**
	 * @var IPermissionService
	 */
	private $_permissionService;

	/**
	 * @var IAttributeService
	 */
	private $_attributeService;

	/**
	 * @var IUserRepository
	 */
	private $_userRepository;

	public function __construct(IResourceRepository $resourceRepository
			//IPermissionService $permissionService,
			//IAttributeService $attributeService,
			//IUserRepository $userRepository
			)
	{
		$this->_resourceRepository = $resourceRepository;
		//$this->_permissionService = $permissionService;
		//$this->_attributeService = $attributeService;
		//$this->_userRepository = $userRepository;
	}

	public function getScheduleResources($scheduleId, $includeInaccessibleResources, JUser $user, $filter = null)
	{
		if ($filter == null)
		{
			$filter = new RFScheduleResourceFilter();
		}

		$resources = $this->_resourceRepository->getScheduleResources($scheduleId, $includeInaccessibleResources, $user);
		//$resourceIds = $filter->FilterResources($resources, $this->_resourceRepository, $this->_attributeService);

		return $resources; 
		//$this->Filter($resources, $user, $includeInaccessibleResources, $resourceIds);
	}

	public function getAllResources($includeInaccessibleResources, JUser $user)
	{
		$resources = $this->_resourceRepository->getResourceList($includeInaccessibleResources, $user);

		return $resources;
	}

	/**
	 * @param $resources array|BookableResource[]
	 * @param $user UserSession
	 * @param $includeInaccessibleResources bool
	 * @param int[] $resourceIds
	 * @return array|ResourceDto[]
	 */
	/*
	private function Filter($resources, $user, $includeInaccessibleResources, $resourceIds = null)
	{
		$filter = new ResourcePermissionFilter($this->_permissionService, $user);
		$statusFilter = new ResourceStatusFilter($this->_userRepository, $user);

		$resourceDtos = array();
		foreach ($resources as $resource)
		{
			if (is_array($resourceIds) && !in_array($resource->GetId(), $resourceIds))
			{
				continue;
			}

			$canAccess = $filter->ShouldInclude($resource);

			if (!$includeInaccessibleResources && !$canAccess)
			{
				continue;
			}

			if ($canAccess)
			{
				$canAccess = $statusFilter->ShouldInclude($resource);
			}

			$resourceDtos[] = new ResourceDto($resource->GetResourceId(), $resource->GetName(), $canAccess, $resource->GetScheduleId(), $resource->GetMinLength());
		}

		return $resourceDtos;
	}*/

	public function getAccessories()
	{
		return $this->_resourceRepository->GetAccessoryList();
	}

	public function getResourceGroups($scheduleId, JUser $user)
	{
		//$filter = new CompositeResourceFilter();
		//$filter->Add(new ResourcePermissionFilter($this->_permissionService, $user));
		//$filter->Add(new ResourceStatusFilter($this->_userRepository, $user));

		$groups = $this->_resourceRepository->getResourceGroups($scheduleId);

		return $groups;
	}

	public function getResourceTypes()
	{
		return $this->_resourceRepository->getResourceTypes();
	}

	/**
	 * @return Attribute[]
	 */
	public function getResourceAttributes()
	{
		$attributes = array();
		$customAttributes = $this->_attributeService->getByCategory(CustomAttributeCategory::RESOURCE);
		foreach ($customAttributes as $ca)
		{
			$attributes[] = new Attribute($ca);
		}

		return $attributes;
	}

	/**
	 * @return Attribute[]
	 */
	public function getResourceTypeAttributes()
	{
		$attributes = array();
		$customAttributes = $this->_attributeService->getByCategory(CustomAttributeCategory::RESOURCE_TYPE);
		foreach ($customAttributes as $ca)
		{
			$attributes[] = new Attribute($ca);
		}

		return $attributes;
	}
}