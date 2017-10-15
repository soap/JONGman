<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFResourceGroup
{
	const RESOURCE_TYPE = 'resource';
	const GROUP_TYPE = 'group';

	public $id;
	public $name;
	public $label;
	public $parent;
	public $parent_id;
	/**
	 * @var RFResourceGroup[]|RFResourceGroupAssignment[]
	 */
	public $children = array();
	public $type = RFResourceGroup::GROUP_TYPE;

	public function __construct($id, $name, $parentId = null)
	{
		$this->withId($id);
		$this->setName($name);
		$this->parent_id = $parentId;
	}

	/**
	 * @param $resourceGroup RFResourceGroup
	 */
	public function addChild(RFResourceGroup &$resourceGroup)
	{
		$resourceGroup->parent_id = $this->id;
		$this->children[] = $resourceGroup;
	}

	/**
	 * @param $assignment RFResourceGroupAssignment
	 */
	public function addResource(RFResourceGroupAssignment &$assignment)
	{
		$this->children[] = $assignment;
	}

	/**
	 * @param string $groupName
	 * @param int $parentId
	 * @return ResourceGroup
	 */
	public static function create($groupName, $parentId = null)
	{
		return new RFResourceGroup(null, $groupName, $parentId);
	}

	/**
	 * @param int $id
	 */
	public function withId($id)
	{
		$this->id = $id;
	}

	public function setName($name)
	{
		$this->name = $name;
		$this->label = $name;
	}

	/**
	 * @param int $targetId
	 */
	public function moveTo($targetId)
	{
		$this->parent_id = $targetId;
	}

	public function rename($newName) {
		$this->setName($newName);
	}
}

