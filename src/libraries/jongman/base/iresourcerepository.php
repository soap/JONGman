<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC');

interface IResourceRepository
{
	/**
	 * Gets all Resources for the given scheduleId
	 *
	 * @param int $scheduleId
	 * @return array|RFResourceBookable[]
	 */
	public function getScheduleResources($scheduleId);

	/**
	 * @param int $resourceId
	 * @return RFBookableResource
	*/
	public function loadById($resourceId);

	/**
	 * @param string $publicId
	 * @return RFBookableResource
	*/
	public function loadByPublicId($publicId);

	/**
	 * @param RFBookableResource $resource
	 * @return int ID of created resource
	*/
	public function add(RFResourceBookable $resource);

	/**
	 * @param BookableResource $resource
	*/
	public function update(RFResourceBookable $resource);

	/**
	 * @param RFBookableResource $resource
	*/
	public function delete(RFResourceBookable $resource);

	/**
	 * @return array|RFResourceBookable[] array of all resources
	 * @In used
	*/
	public function getResourceList($includeInaccessibleResources = true, $user=null);

	/**
	 * @param int $pageNumber
	 * @param int $pageSize
	 * @param string|null $sortField
	 * @param string|null $sortDirection
	 * @param ISqlFilter $filter
	 * @return PageableData|BookableResource[]
	*/
	public function getList($pageNumber, $pageSize, $sortField = null, $sortDirection = null, $filter = null);

	/**
	 * @abstract
	 * @return array|Accessoryto[] all accessories
	*/
	public function getAccessoryList();

	/**
	 * @param int|null $scheduleId
	 * @param IResourceFilter|null $resourceFilter
	 * @return RFResourceGroupTree
	*/
	public function getResourceGroups($scheduleId = null, $resourceFilter = null);

	/**
	 * @param int $resourceId
	 * @param int $groupId
	*/
	public function addResourceToGroup($resourceId, $groupId);

	/**
	 * @param int $resourceId
	 * @param int $groupId
	*/
	public function removeResourceFromGroup($resourceId, $groupId);

	/**
	 * @param RFResourceGroup $group
	 * @return RFResourceGroup
	*/
	public function addResourceGroup(RFResourceGroup $group);

	/**
	 * @param int $groupId
	 * @return RFResourceGroup
	*/
	public function loadResourceGroup($groupId);

    /**
     * @param RFResourceGroup $group
	*/
	public function updateResourceGroup(RFResourceGroup $group);

	/**
	 * @param $groupId
	*/
	public function deleteResourceGroup($groupId);

	/**
	 * @return RFResourceType[]|array
	*/
	public function getResourceTypes();

	/**
	 * @param int $resourceTypeId
	 * @return RFResourceType
	*/
	public function loadResourceType($resourceTypeId);

	/**
	 * @param RFResourceType $type
	 * @return int
	*/
	public function addResourceType(RFResourceType $type);

	/**
	 * @param RFResourceType $type
	*/
	public function updateResourceType(RFResourceType $type);

	/**
	 * @param int $id
	*/
	public function removeResourceType($id);

	/**
	 * @return RFResourceStatusReason[]
	*/
	public function getStatusReasons();

	/**
	 * @param int $statusId
	 * @param string $reasonDescription
	 * @return int
	*/
	public function addStatusReason($statusId, $reasonDescription);

	/**
	 * @param int $reasonId
	 * @param string $reasonDescription
	*/
	public function updateStatusReason($reasonId, $reasonDescription);

	/**
	 * @param int $reasonId
	*/
	public function removeStatusReason($reasonId);
}