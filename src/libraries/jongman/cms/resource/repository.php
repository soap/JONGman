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
		    $row = new JRegistry($row->params);
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
	 * @return RFBookableResource
	*/
	public function loadByPublicId($publicId)
	{
		
	}
	
	/**
	 * @param RFBookableResource $resource
	 * @return int ID of created resource
	*/
	public function add(RFResourceBookable $resource)
	{
		
	}
	
	/**
	 * @param RFBookableResource $resource
	*/
	public function update(RFResourceBookable $resource)
	{
		
	}
	
	/**
	 * @param RFBookableResource $resource
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
	 * @return BookableResource[]
	*/
	public function getList($pageNumber, $pageSize, $sortField = null, $sortDirection = null, $filter = null)
	{
		
	}
	/**
	 * @abstract
	 * @return array|Accessoryto[] all accessories
	*/
	public function getAccessoryList()
	{
		
	}
	/**
	 * @param int|null $scheduleId
	 * @param IResourceFilter|null $resourceFilter
	 * @return RFResourceGroupTree
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
	 * @param RFResourceGroup $group
	 * @return RFResourceGroup
	*/
	public function addResourceGroup(RFResourceGroup $group)
	{
		
	}
	
	/**
	 * @param int $groupId
	 * @return RFResourceGroup
	*/
	public function loadResourceGroup($groupId)
	{
		
	}
	
	/**
	 * @param RFResourceGroup $group
	*/
	public function updateResourceGroup(RFResourceGroup $group)
	{
		
	}
	
	/**
	 * @param $groupId
	*/
	public function deleteResourceGroup($groupId)
	{
		
	}
	
	/**
	 * @return RFResourceType[]|array
	*/
	public function getResourceTypes()
	{
		
	}
	/**
	 * @param int $resourceTypeId
	 * @return RFResourceType
	*/
	public function loadResourceType($resourceTypeId)
	{
		
	}
	
	/**
	 * @param RFResourceType $type
	 * @return int
	*/
	public function addResourceType(RFResourceType $type)
	{
		
	}
	
	/**
	 * @param RFResourceType $type
	*/
	public function updateResourceType(RFResourceType $type)
	{
		
	}
	
	/**
	 * @param int $id
	*/
	public function removeResourceType($id)
	{
		
	}

	/**
	 * @return RFResourceStatusReason[]
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