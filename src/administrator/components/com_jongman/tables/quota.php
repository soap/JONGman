<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
jimport('joomla.database.table');

/**
 * Quota table.
 *
 * @package     JONGman
 * @subpackage  Admin
 * @since       1.0
 */
class JongmanTableQuota extends JTable
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabase  $db  A database connector object.
	 *
	 * @return  JongmanTableQuota
	 * @since   1.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__jongman_quotas', 'id', $db);
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array   $array   The input array to bind.
	 * @param   string  $ignore  A list of fields to ignore in the binding.
	 *
	 * @return  null|string	null is operation was satisfactory, otherwise returns an error
	 * @see     JTable:bind
	 * @since   1.0
	 */
	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params'])) {
			$registry = new JRegistry();
			$registry->loadArray($array['params']);
			$array['params'] = (string) $registry;
		}

		return parent::bind($array, $ignore);
	}

	/**
	 * Overloaded check method to ensure data integrity.
	 *
	 * @return  boolean  True on success.
	 * @since   1.0
	 */
	public function check()
	{
		// Check for valid name.
		if (trim($this->quota_limit) == 0) {
			$this->setError(JText::_('COM_JONGMAN_ERROR_QUOTA_LIMIT_NUMBER'));
			return false;
		}

		return true;
	}

	/**
	 * Overload the store method for the jongman_quotas table.
	 *
	 * @param   boolean  $updateNulls  Toggle whether null values should be updated.
	 *
	 * @return  boolean  True on success, false on failure.
	 * @since   1.0
	 */
	public function store($updateNulls = false)
	{
		// Initialiase variables.
		$date	= JFactory::getDate()->toSQL();
		$userId	= JFactory::getUser()->get('id');

		if (empty($this->id)) {
			// New record.
			$this->created_time		= $date;
			$this->created_user_id	= $userId;
		} 
		else {
			// Existing record.
			$this->modified_time	= $date;
			$this->modified_user_id	= $userId;
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}
}