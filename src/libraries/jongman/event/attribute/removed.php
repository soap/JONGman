<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
class RFEventAttributeRemoved extends RFSeriesEvent
{
	/**
	 * @return int
	 */
	public function attributeId()
	{
		return $this->attribute->attributeId;
	}

	/**
	 * @var \RFAttributeValue
	 */
	private $attribute;

	public function __construct(RFAttributeValue $attribute, RFReservationExistingSeries $series)
	{
		$this->attribute = $attribute;

		parent::__construct($series, RFEventPriority::Highest);
	}

	public function __toString()
	{
		return sprintf("%s%s", get_class($this), $this->attribute->__toString());
	}
}