<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');
jimport('jongman.base.ireservationsaveresultview');

/**
 * Reservation model.
 *
 * @package     JONGman
 * @subpackage  Admin
 * @since       2.0
 */
class JongmanModelReservation extends JModelAdmin implements IReservationPage, /*, IReservationApprovalPage, */ IReservationSaveResultView
{
	protected $validData = array();
	private $_handler; 
	private $_saveresult = false;
	private $_warnings = array();
	private $_saveSuccessfully = false;
	
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
		$pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');
		$instance = JTable::getInstance('Instance', 'JongmanTable');
		if ($pk > 0)
		{
			// Attempt to load the row.
			$return = $instance->load($pk);
		
			// Check for a table object error.
			if ($return === false && $table->getError())
			{
				$this->setError($instance->getError());
		
				return false;
			}
		}
		
		$reservationId = $instance->reservation_id;
		if ($result = parent::getItem($reservationId)) {
			$tz = RFApplicationHelper::getUserTimezone();
			if (empty($pk)) {
				$result->owner_id = JFactory::getUser()->get("id");
				$result->repeat_type="none";
				
				// it will be conveted to user time zone in form (by field)
				$date = JFactory::getDate('now', $tz);
				$date->setTimezone(new DateTimeZone('UTC'));
				$result->start_date 	= $date->format('Y-m-d');
				$result->start_time 	= $date->format('H:i:s');
					
				// it will be conveted to user time zone in form (by field)
				$date = JFactory::getDate('now', $tz);
				$date->setTimezone(new DateTimeZone('UTC'));
				$result->end_date 	= $date->format('Y-m-d');
				$result->end_time 	= $date->format('H:i:s');
			}else{
				$result->start_date = JDate::getInstance($instance->start_date)->format('Y-m-d');
				$result->end_time = JDate::getInstance($instance->start_date)->format('H:i');
			
				$result->end_date = JDate::getInstance($instance->end_date)->format('Y-m-d');
				$result->end_time = JDate::getInstance($instance->end_date)->format('H:i');
			}	
			$result->reference_number = $instance->reference_number;
			$result->instance_id = $instance->id;
			$result->reservation_id = $instance->reservation_id;
			$result->repeat_options = new JRegistry($result->repeat_options);
			
			$result->id = $instance->id;
			
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
		$pk = 0;
		if (isset($data)) {
			//var_dump($data);
			if (is_array($data) && isset($data['id'])) {
				$pk = $data['id'];
			}
			if (is_object($data) && isset($data->id)) {
				$pk = (int)$data->id;
			}
			if ($pk > 0) {
				$form->setFieldAttribute('repeat_type', 'disabled', 'true');
			}
		}
		
		parent::preprocessForm($form, $data, $group);
	}
	
	public function save($data)
	{
		
	}
	
	public function update($data)
	{
		
	}
	
	public function approve($cid, $value)
	{
		$id = (int) $cid[0];
		$item = $this->getItem($id);
		$this->validData['reference_number'] = $item->reference_number;
		
		$user = JFactory::getUser();
		$reservationAction = RFReservationAction::Approve;
		$factory = new RFFactoryReservationPersistence();
		$persistenceService = $factory->create($reservationAction);
		$handler = RFReservationHandler::create($reservationAction, $persistenceService, $user);
		$authService = new RFReservationAuthorisation(new RFAuthorisationService($user));
		$model = new RFReservationModelApproval($this, $persistenceService, $handler, $authService, $user);
		$model->approve();
		
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
	
	/**
	 * @since 3.0.0
	 * @see JModelAdmin::delete()
	 */
	public function deleteById($id, $scope='deleteinstance')
	{
		//we have to build validData from DB!!
		$this->validData = array();
	
		$item = $this->getItem($id);
		$this->validData = (array)$item;
		$this->validData['updatescope'] = $scope;
	
		$user = JFactory::getUser();
		$reservationAction = RFReservationAction::Delete;
	
		$persistenceFactory = new RFFactoryReservationPersistence();
		$persistenceService = $persistenceFactory->create($reservationAction);
	
		$handler = $this->getHandler($reservationAction);
	
		$subModel = new RFReservationModelDelete($this,
				$persistenceService,
				$handler,
				$user);
	
		$reservationSeries = $subModel->buildReservation();
		$subModel->handleReservation($reservationSeries);
	
		if (!$this->_saveresult) {
			// error already set in $this->setErrors(), get called in RFReservationModelDelete class
			return false;
		}
	
		return true;
	}
	
	/**
	 *
	 * Get reservation handler based on Reservation Action to handle reservation request
	 * @param RFReservationAction $reservationAction
	 * @return IReservationHandler
	 * @since JONGman 3.0
	 */
	protected function getHandler($reservationAction)
	{
		$user = JFactory::getUser();
		/* we should set userSession properties!!! */
	
		$persistenceFactory = new RFFactoryReservationPersistence();
		$persistenceService = $persistenceFactory->create($reservationAction);
	
		$handler = RFReservationHandler::create($reservationAction, $persistenceService, $user);
	
		return $handler;
	}
	
	/** ============ IRepeatOptionsComposite ================================== **/
	/**
	 * @abstract
	 * @return string
	 */
	public function getRepeatType()
	{
		if (!isset($this->validData['repeat_type'])) return 'none';
	
		return $this->validData['repeat_type'];
	}
	
	/**
	 * @abstract
	 * @return string|null
	 */
	public function getRepeatInterval()
	{
		if (!isset($this->validData['repeat_interval']))  return 1;
	
		return $this->validData['repeat_interval'];
	}
	
	/**
	 * @abstract
	 * @return int[]|null
	 */
	public function getRepeatWeekdays()
	{
		return $this->validData['repeat_days'];
	}
	
	/**
	 * @abstract
	 * @return string|null
	 */
	public function getRepeatMonthlyType()
	{
		return $this->validData['repeat_monthly_type'];
	}
	
	/**
	 * @abstract
	 * @return string|null
	 */
	public function getRepeatTerminationDate()
	{
		return $this->validData['repeat_terminated'];
	}
	
	/** ===== ============================================= **/
	public function getReferenceNumber()
	{
		$referenceNumber = $this->validData['reference_number'];
		return $referenceNumber;
	}
	
	public function getSeriesUpdateScope()
	{
		return $this->validData['updatescope'];
	}
	
	/** ====== IReservationPage =========================== **/
	
	public function getUserId()
	{
		return $this->validData['owner_id'];
	}
	
	
	public function getCustomerId()
	{
		return $this->validData['customer_id'];
	}
	/**
	 * @return int
	 */
	public function getResourceId()
	{
		return $this->validData['resource_id'];
	}
	
	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->validData['title'];
	}
	
	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->validData['description'];
	}
	
	/**
	 * @return string
	 */
	public function getStartDate()
	{
		return $this->validData['start_date'];
	}
	
	/**
	 * @return string
	 */
	public function getEndDate()
	{
		return $this->validData['end_date'];
	}
	
	/**
	 * @return string
	 */
	public function getStartTime()
	{
		return $this->validData['start_time'];
	}
	/**
	 * @return string
	 */
	public function getEndTime()
	{
		return $this->validData['end_time'];
	}
	
	/**
	 * @return int[]
	 */
	public function getResources()
	{
		return array();
	}

	/** ====== IReservationSaveResultView ================= **/
	/**
	 * @param bool $succeeded
	 */
	public function setSaveSuccessfulMessage($succeeded)
	{
		$this->_saveresult = $succeeded;
	}
	
	/**
	 * @param array|string[] $errors
	 */
	public function setErrors($errors)
	{
		foreach($errors as $error) {
			$error = trim ($error);
			if (!empty($error)) $this->setError($error);
		}
	}
	
	/**
	 * @param array|string[] $warnings
	 */
	public function setWarnings($warnings)
	{
		array_push($this->_warnings, $warnings);
	}	
}

