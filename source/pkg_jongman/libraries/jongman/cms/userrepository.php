<?php
defined('_JEXEC') or die;
 
jimport('jongman.base.iuserrepository');
class RFUserRepository implements IUserRepository
{

	public function loadById($userId)
	{
		return JFactory::getUser($userId);
	}
}