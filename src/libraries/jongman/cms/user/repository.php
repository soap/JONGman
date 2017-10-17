<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

jimport('jongman.base.iuserrepository');

class RFUserRepository implements IUserRepository
{

	public function loadById($userId)
	{
		return JFactory::getUser($userId);
	}
}