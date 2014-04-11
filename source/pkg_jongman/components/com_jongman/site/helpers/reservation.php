<?php
/**
 * @version: $Id$
 */

defined('_JEXEC') or die;

class ReservationHelper 
{
	/**
	 * 
	 * Check if user can edit reservation
	 * @param unknown_type $record
	 */
	public static function canEdit($record)
	{
		$user = JFactory::getUser();
		if (is_array($record) && isset($record['id'])) {
			
			
		}else if (($record instanceof JObject) && isset($record->resource_id)) {
			$resource_id = (int)$record->resource_id;
		}else{
			$resource_id = 0;
		}
		if ($resource_id > 0) {
			$asset = 'com_jongman.resource.'.$record->resource_id;
		}else{
			$asset = 'com_jongman';
			return $user->authorise('core.edit', $asset);
		} 
		
		if ($user->authorise('core.edit', $asset)) {
			return true;	
		}else{
			if (($record->reserved_for == $user->id) || ($record->created_by == $user->id)) {
				return $user->auhorise('core.edit.own', $asset);	
			} 	
		}

		return false;
	}  
	
	public static function canDelete($record) 
	{
		$result = false;
		if ($record->id == 0) return false;
		
		$user = JFactory::getUser();
		if ($user->authorise('core.delete', 'com_jongman.resource.'.$record->resource_id)) 
		{
			return true;
		}

		if (($record->reserved_for == $user->id) || ($record->created_by == $user->id))
		{
			return true;	
		}
		
		return $result;
	}	
}