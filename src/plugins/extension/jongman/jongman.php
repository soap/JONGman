<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
 JONGman - Visualized Reservation System extension for Joomla
 ------------------------------------------------------------------------
 @Author    Prasit Gebsaap
 @Website   http://www.joomlant.com
 @Copyright Copyright (C) 2013 - 2015 Prasit Gebsaap. All Rights Reserved.
 @License   GNU General Public License version 3, or later
 ------------------------------------------------------------------------*/
defined('_JEXEC') or die;

class plgExtensionJongman extends JPlugin
{

	/**
	 * Allow to processing of Reservation data after it is saved.
	 *
	 * @param   object    $data The data representing the Reservation.
	 * @param   object    $table
	 * @param   boolean   $result
	 * @param   boolean   $isNew True is this is new data, false if it is existing data.
	 *
	 * @throws 	Exception
	 * @return  boolean
	 * @since   3.0
	 */
	function  onReservationSeriesAfterSave($data, $table, $result, $isNew)
	{
		$dbo = JFactory::getDbo();
		$query = $dbo->getQuery(true);
	
		// Process extra fields
		if ($table->id && $result && isset($data['reservation_custom_fields']) && (count($data['reservation_custom_fields']))) {
			try {
	
				$query->clear();
				$query->delete()->from($dbo->quoteName('#__jongman_reservation_fields'));
				$query->where('reservation_id = '.$table->id);
				$query->where("field_key LIKE 'reservation_custom_fields.%'");
				$dbo->setQuery($query);
	
				if (!$dbo->execute()) {
					throw new Exception($dbo->getErrorMsg());
				}
	
				$tuples = array();
				$order	= 1;
	
				foreach ($data['reservation_custom_fields'] as $k => $v)
				{
					$tuples[] = '('.$table->id.', '.$dbo->quote('reservation_custom_fields.'.$k).', '.$dbo->quote($v).', '.$order++.')';
				}
				
				$dbo->setQuery('INSERT INTO '.$dbo->quoteName('#__jongman_reservation_fields').' VALUES '.implode(', ', $tuples));
				
				if (!$dbo->execute()) {
					throw new Exception($dbo->getErrorMsg());
				}
	
			} catch (JException $e) {
				$this->_subject->setError($e->getMessage());
				return false;
			}
		}
		// end of extra field processing
	}
	
	/**
	 * @param	JForm	$form	The form to be altered.
	 * @param	array	$data	The associated data for the form.
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function onReservationSeriesPrepareForm($form, $data)
	{
		// Load JONGman plugin language
		$lang = JFactory::getLanguage();
		$lang->load('plg_extension_jongman', JPATH_BASE.'/plugins/extension/jongman');
	
		if (!($form instanceof JForm)) {
			$this->_subject->setError('JERROR_NOT_A_FORM');
			return false;
		}
	
		// Check we are manipulating a valid form.
		if (!in_array($form->getName(), array('com_jongman.reservation', 'com_jongman.instance'))) {
			return true;
		}
	
		// Add the registration fields to the form.
		JForm::addFormPath(__DIR__.'/fields');
		$form->loadFile('reservation', false);
	}
	
	/**
	 * @param	string	$context	The context for the data
	 * @param	int		$data		The user id
	 *
	 * @return	boolean
	 * @since	1.6
	 */
	public function onReservationSeriesPrepareData($context, $data)
	{
		// Check we are manipulating a valid form.
		if (!in_array($context, array('com_jongman.reservation', 'com_jongman.reservationitem')))
		{
			return true;
		}
	
		if (is_object($data))
		{
			if ($context == 'com_jongman.reservationitem') {
				$reservationId = isset($data->reservation_id) ? $data->reservation_id : 0;	
			}else{
				if (isset($data->reservation_id)) {
					$reservationId = $data->reservation_id;
				}else{
					$reservationId = isset($data->id) ? $data->id : 0;
				}
			}
			// Load the custom fields data from the database.
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('field_key, field_value')->from($db->quoteName('#__jongman_reservation_fields'));
			$query->where('reservation_id = '.(int) $reservationId);
			$query->where("field_key LIKE 'reservation_custom_fields.%'");
			$db->setQuery($query);
	
			try
			{
				$results = $db->loadRowList();
			}
			catch (RuntimeException $e)
			{
				$this->_subject->setError($e->getMessage());
				return false;
			}
	
			// Merge the custom fields data into current form data
			$data->reservation_custom_fields = array();
	
			foreach ($results as $v)
			{
				$k = str_replace('reservation_custom_fields.', '', $v[0]);
				$data->reservation_custom_fields[$k] = $v[1];
			}
	
		}
		return true;
	}
}