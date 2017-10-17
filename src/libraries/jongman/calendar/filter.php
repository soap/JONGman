<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFCalendarFilter
{
	/**
	 * @var array|RFCalendarFilter[]
	 */
	private $filters = array();

	/**
	 * @var string
	*/
	private $type;

	/**
	 * @var string
	 */
	private $id;

	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var bool
	 */
	private $selected;

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}

	/**
	 * @return bool
	 */
	public function selected()
	{
		return $this->selected;
	}

	public function __construct($type, $id, $name, $selected)
	{
		$this->type = $type;
		$this->id = $id;
		$this->name = $name;
		$this->selected = $selected;
	}

	public function addSubFilter(RFCalendarFilter $subfilter)
	{
		$this->filters[] = $subfilter;
	}

	/**
	 * @return array|RFCalendarFilter[]
	 */
	public function getFilters()
	{
		return $this->filters;
	}

}