<?php
/**
 * @version     $Id$
 * @package     JONGman
 * @subpackage  Admin
 * @copyright   Copyright 2012 Prasit Gebsaap. All rights reserved.
 * @license     GNU General Public License version 2 or later.
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Reservation model.
 *
 * @package     JONGman
 * @subpackage  Admin
 * @since       2.0
 */
class JongmanModelReservation extends JModelAdmin
{
	/**
	 * Method to get the Reservation form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 * @since   2.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			$this->option.'.reservation',
			'reservation',
			array('control' => 'jform', 'load_data' => $loadData)
		);

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a Reservation.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed    Category data object on success, false on failure.
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($result = parent::getItem($pk)) {

			// Convert the created and modified dates to local user time for display in the form.
			jimport('joomla.utilities.date');
			$tz	= new DateTimeZone(JFactory::getApplication()->getCfg('offset'));
		
			if (intval($result->created)) {
				$date = new JDate($result->created);
				//$date->setTimezone($tz);
				$result->created_time = $date->toSql();
			}
			else {
				$result->created = null;
			}

			if (intval($result->modified)) {
				$date = new JDate($result->modified);
				//$date->setTimezone($tz);
				$result->modified = $date->toSql();
			}
			else {
				$result->modified = null;
			}
			
			if (empty($pk)) {
				$result->owner_id = JFactory::getUser()->get("id");
			}
		}

		return $result;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name.
	 * @param   array   $config  Configuration array for model.
	 *
	 * @return  JTable  A database object
	 * @since   2.0
	 */
	public function getTable($type = 'Reservation', $prefix = 'JongmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   2.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->option.'.edit.reservation.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}
		return $data;
	}

	function preprocessForm(JForm $form, $data, $group = null)
	{
		if (isset($data)) {
		}
		parent::preprocessForm($form, $data, $group);
	}
	
	public function approve($cid, $value)
	{
		$user = JFactory::getUser();
		$reservationAction = RFReservationAction::Approve;
		$factory = new RFFactoryReservationPersistence();
		$persistenceService = $factory->create($reservationAction);
		$handler = RFReservationHandler::create($reservationAction, $persistenceService, $user);
		
		$model = new RFReservationModelApproval($this, $persistenceService, $handler, $auth, ServiceLocator::GetServer()->GetUserSession());
		$presenter->PageLoad();
		
		
		return array();	
	}
	
	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  The table object for the record.
	 *
	 * @return  boolean  True if successful, otherwise false and the error is set.
	 * @since   2.0
	 */
	protected function prepareTable($table)
	{

	}
	
}

