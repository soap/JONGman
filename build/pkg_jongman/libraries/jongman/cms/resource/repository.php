<?php
defined('_JEXEC') or die;

class RFResourceRepository implements IResourceRepository
{
	/**
	 * Gets all Resources for the given scheduleId
	 *
	 * @param int $scheduleId
	 * @return array|RFResourceBookable[]
	 */
	public function getScheduleResources($scheduleId)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
		$query->select('*')
			->from('#__jongman_resources AS rs')
			->where('rs.schedule_id='.$scheduleId);
		$dbo->setQuery($query);
		$rows = $dbo->loadObjectList();
		
		$resources = array();
		foreach($rows as $row) 
		{
			$resources[] = RFResourceBookable::create($row);
		}

		return $resources;
	}
	
	/**
	 * @param int $resourceId
	 * @return RFResourceBookable
	*/
	public function loadById($resourceId)
	{
		$table = JTable::getInstance('Resource', 'JongmanTable');
		$table->load($resourceId);

		$resource = RFResourceBookable::create($table);
		
		return $resource;
	}
	
	/**
	 * @param string $publicId
	 * @return BookableResource
	*/
	public function loadByPublicId($publicId)
	{
		
	}
	
	/**
	 * @param BookableResource $resource
	 * @return int ID of created resource
	*/
	public function add(RFResourceBookable $resource)
	{
		
	}
	
	/**
	 * @param BookableResource $resource
	*/
	public function update(RFResourceBookable $resource)
	{
		
	}
	
	/**
	 * @param BookableResource $resource
	*/
	public function delete(RFResourceBookable $resource)
	{
		
	}
	
	/**
	 * @return array|RFResourceBookable[] array of all resources
	*/
	public function getResourceList($includeInaccessibleResources = true, $user = null)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		
		$query->select('a.*')
			->from('#__jongman_resources AS a');
		
		if ($user === null) {
			$user = JFactory::getUser();
		}
		
		if (!$includeInaccessibleResources) {
			$query->where('a.published=1');
			$viewLevels = join(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN ('.$viewLevels.')');
		}
		
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		
		$resources = array();
		foreach($rows as $row) {
			$resource = RFResourceBookable::create($row);
			$resources[] = $resource;
		}
		
		return $resources;
	}
	/**
	 * @param int $pageNumber
	 * @param int $pageSize
	 * @param string|null $sortField
	 * @param string|null $sortDirection
	 * @param ISqlFilter $filter
	 * @return PageableData|BookableResource[]
	*/
	public function getList($pageNumber, $pageSize, $sortField = null, $sortDirection = null, $filter = null)
	{
		
	}
	/**
	 * @abstract
	 * @return array|AccessoryDto[] all accessories
	*/
	public function getAccessoryList()
	{
		
	}
	/**
	 * @param int|null $scheduleId
	 * @param IResourceFilter|null $resourceFilter
	 * @return ResourceGroupTree
	*/
	public function getResourceGroups($scheduleId = null, $resourceFilter = null)
	{
		
	}
	
	/**
	 * @param int $resourceId
	 * @param int $groupId
	*/
	public function addResourceToGroup($resourceId, $groupId)
	{
		
	}
	
	/**
	 * @param int $resourceId
	 * @param int $groupId
	*/
	public function removeResourceFromGroup($resourceId, $groupId)
	{
		
	}
	
	/**
	 * @param ResourceGroup $group
	 * @return ResourceGroup
	*/
	public function addResourceGroup(ResourceGroup $group)
	{
		
	}
	
	/**
	 * @param int $groupId
	 * @return ResourceGroup
	*/
	public function loadResourceGroup($groupId)
	{
		
	}
	
	/**
	 * @param ResourceGroup $group
	*/
	public function updateResourceGroup(ResourceGroup $group)
	{
		
	}
	
	/**
	 * @param $groupId
	*/
	public function deleteResourceGroup($groupId)
	{
		
	}
	
	/**
	 * @return ResourceType[]|array
	*/
	public function getResourceTypes()
	{
		
	}
	/**
	 * @param int $resourceTypeId
	 * @return ResourceType
	*/
	public function loadResourceType($resourceTypeId)
	{
		
	}
	
	/**
	 * @param ResourceType $type
	 * @return int
	*/
	public function addResourceType(ResourceType $type)
	{
		
	}
	
	/**
	 * @param ResourceType $type
	*/
	public function updateResourceType(ResourceType $type)
	{
		
	}
	
	/**
	 * @param int $id
	*/
	public function removeResourceType($id)
	{
		
	}
	
	/**
	 * @return ResourceStatusReason[]
	*/
	public function getStatusReasons()
	{
		
	}
	
	/**
	 * @param int $statusId
	 * @param string $reasonDescription
	 * @return int
	*/
	public function addStatusReason($statusId, $reasonDescription)
	{
		
	}
	
	/**
	 * @param int $reasonId
	 * @param string $reasonDescription
	*/
	public function updateStatusReason($reasonId, $reasonDescription)
	{
		
	}
	
	/**
	 * @param int $reasonId
	*/
	public function removeStatusReason($reasonId)
	{
		
	}
}