<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

class RFQueryHelper
{
	/**
	 * Method to create query filter conditions
	 *
	 * @param     object    $query      The query object to work with
	 * @param     array     $filters    The field/value pairs to filter by
	 *
	 * @return    void
	 */
	public static function buildFilter(&$query, $filters = array())
	{
		$db = JFactory::getDbo();

		foreach($filters AS $field => $filter)
		{
			if (count($filter) != 2) continue;

			list($type, $value) = $filter;

			switch (strtoupper($type))
			{
				case 'STR-EQUALS':
					if (!empty($value)) $query->where($field . ' = ' . $db->Quote($db->escape($value, true)));
					break;

				case 'STR-LIKE':
					if (!empty($value)) $query->where($field . ' LIKE ' . $db->Quote('%' . $db->escape($value, true) . '%'));
					break;

				case 'SEARCH':
					if (!empty($value)) {
						if (stripos($value, 'id:') === 0) {
							$query->where($field . '.id = ' .(int) substr($value, 4));
						}
						elseif (stripos($value, 'author:') === 0) {
							$value = $db->Quote('%' . $db->escape(trim(substr($value, 8)), true) . '%');
							$query->where('(u.name LIKE ' . $value . ' OR u.username LIKE ' . $value . ')');
						}
						else {
							$value = $db->Quote('%' . $db->escape($value, true) . '%');
							$query->where('(' . $field . '.title LIKE ' . $value . ' OR ' . $field . '.alias LIKE ' . $value . ')');
						}
					}
					break;

				case 'STATE':
					if (is_numeric($value)) {
						$query->where($field . ' = ' . (int) $value);
					}
					elseif ($value === '') {
						if (JFactory::getApplication()->isSite()) {
							// Frontend defaults to published only
							$query->where($field . ' = 1');
						}
						else {
							// Backend defaults to published and unpublished
							$query->where('(' . $field . ' = 0 OR ' . $field . ' = 1)');
						}
					}
					else {
						$query->where('' . $field . ' = 1');
					}
					break;

				case 'INT-NOTZERO':
					if (is_numeric($value) && intval($value) != 0) $query->where($field . ' = ' .(int) $value);
					break;

				case 'INT':
				default:
					if (is_numeric($value)) $query->where($field . ' = ' . (int) $value);
					break;
			}
		}
	}
}