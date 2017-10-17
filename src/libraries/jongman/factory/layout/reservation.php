<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
jimport('jongman.base.ilayoutfactory');
class RFFactoryLayoutReservation implements ILayoutFactory
{
	private $_targetTimezone;

	/**
	 * @param string $targetTimezone target timezone of layout
	 */
	public function __construct($targetTimezone)
	{
		$this->_targetTimezone = $targetTimezone;
	}

	/**
	 * @see ILayoutFactory::CreateLayout()
	 */
	public function createLayout()
	{
		return new RFLayoutReservation($this->_targetTimezone);
	}
}
