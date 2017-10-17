<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFResourceGroupTree
{
	/**
	 * @var $references RFResourceGroup[]
	 */
	protected $references = array();

	/**
	 * @var array|RFResourceGroup[]
	*/
	protected $groups = array();

	/**
	 * @var array|ResourceDto[]
	*/
	protected $resources = array();

	public function addGroup(ResourceGroup $group)
	{
		$this->references[$group->id] = $group;

		// It it's a root node, we add it directly to the tree
		$parent_id = $group->parent_id;
		if (empty($parent_id))
		{
			$this->groups[] = $group;
		}
		else
		{
			// It was not a root node, add this node as a reference in the parent.
			$this->references[$parent_id]->addChild($group);
		}
	}

	public function addAssignment(RFResourceGroupAssignment $assignment)
	{
		if (array_key_exists($assignment->group_id, $this->references))
		{
			$this->resources[$assignment->resource_id] = new RFResourceDto($assignment->resource_id, $assignment->resource_name);
			$this->references[$assignment->group_id]->addResource($assignment);
		}
	}

	/**
	 * @param bool $includeDefaultGroup
	 * @return array|RFResourceGroup[]
	 */
	public function getGroups($includeDefaultGroup = true)
	{
		if ($includeDefaultGroup)
		{
			return $this->groups;
		}
		else
		{
			return array_slice($this->groups, 1);
		}
	}

	/**
	 * @param int $groupId
	 * @param int[] $resourceIds
	 * @return int[]
	 */
	public function getResourceIds($groupId, &$resourceIds = array())
	{
		$group = $this->references[$groupId];

		if (empty($group->children))
		{
			return $resourceIds;
		}

		foreach ($group->children as $child)
		{
			if ($child->type == RFResourceGroup::RESOURCE_TYPE)
			{
				$resourceIds[] = $child->resource_id;
			}
			else
			{
				$this->getResourceIds($child->id, $resourceIds);
			}
		}

		return $resourceIds;
	}

	/**
	 * @param int $groupId
	 * @return ResourceGroup
	 */
	public function getGroup($groupId)
	{
		return $this->references[$groupId];
	}

	/**
	 * @return ResourceDto[] array of resources keyed by their ids
	 */
	public function getAllResources()
	{
		return $this->resources;
	}
}
