<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.modeladmin');

/**
 * Blackout model.
 *
 * @package     JONGman
 * @subpackage  Admin
 * @since       3.0
 */
class JongmanModelBlackout extends JModelAdmin
{
	protected $blackoutRepository;
	protected $reservationViewRepository;
	
	public function __construct($config = array())
	{
		$this->blackoutRepository = new RFBlackoutRepository();
		$this->reservationViewRepository = new RFReservationViewRepository();
		parent::__construct($config);
	}
	/**
	 * Method to get the Blackout form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 * @since   3.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			$this->option.'.'.$this->name,
			$this->getName(),
			array('control' => 'jform', 'load_data' => $loadData)
		);

		if (empty($form)) {
			return false;
		}

		return $form;
	}

	/**
	 * Method to get a Blackout.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed    Category data object on success, false on failure.
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($result = parent::getItem($pk)) {
			
			$user = JFactory::getUser();
			
			jimport('joomla.utilities.date');
			$tz = new Datetimezone(RFApplicationHelper::getUserTimezone($user->get('id')));
			
			if (empty($result->id)) {
				$date = new JDate();
				$date->setTimezone($tz);
				$result->start_date = $date->toSql(false);
				$result->end_date = $date->toSql(false);
				$result->created = $date->toSql(false);
				$result->modified = $this->_db->getNullDate();
				$result->checked_out = 0;
				$result->checked_out_time = $this->_db->getNullDate();
				$result->repeat_type = 'none'; 
				$result->repeat_options = new JRegistry();
				$result->repeat_options->set('repeat_interval', '1');
			}else{
				$result->repeat_options = new JRegistry($result->repeat_options);
			}

			if (intval($result->created)) {
				$date = new JDate($result->created);
				$date->setTimezone($tz);
				$result->created = $date->toSql(true);
			}
			else {
				$result->created = null;
			}

			if (intval($result->modified)) {
				$date = new JDate($result->modified);
				$date->setTimezone($tz);
				$result->modified = $date->toMySQL(true);
			}
			else {
				$result->modified = null;
			}
		}

		return $result;
	}

	/**
	 * A protected method to get a set of ordering conditions.
	 *
	 * @param   JTable  $table  A record object.
	 *
	 * @return  array  An array of conditions to add to add to ordering queries.
	 * @since   3.0
	 */
	protected function getReorderConditions($table = null)
	{
		$condition = array();

		return $condition;
	}

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name.
	 * @param   array   $config  Configuration array for model.
	 *
	 * @return  JTable  A database object
	 * @since   3.0
	 */
	public function getTable($type = 'BlackoutInstance', $prefix = 'JongmanTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 * @since   3.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState($this->option.'.edit.'.$this->getName().'.data', array());

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}

	/** 
	 * Prepare the form before display to the user.
	 *
	 * @param   JForm  $form  The table object for the record.
	 * @param   	   $data  The data binded to $form
	 * @param	string $group group of plugin to trigger
	 * @return  boolean  True if successful, otherwise false and the error is set.
	 * @since   3.0
	 */	 
	 
	protected function preprocessForm(JForm $form, $data, $group='content')
	{
		$user = JFactory::getUser();
		$statesFields = array('published');
		if ( !($user->authorise('core.edit.state', 'com_jongman')) ) {
			foreach($stateFields as $field) {
				$form->setFieldAttribute($field, 'disabled', 'true');
				$form->setFieldAttribute($field, 'filter', 'unset');
			}
		}
		
		parent::preprocessForm($form, $data, $group);
	}
	
	/** 
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  The table object for the record.
	 *
	 * @return  boolean  True if successful, otherwise false and the error is set.
	 * @since   3.0
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		// Clean up keywords -- eliminate extra spaces between phrases
		// and cr (\r) and lf (\n) characters from string
		if (!empty($this->metakey)) {
			// Only process if not empty.

			// array of characters to remove.
			$strip = array("\n", "\r", '"', '<', '>');
			
			// Remove bad characters.
			$clean = JString::str_ireplace($strip, ' ', $this->metakey); 

			// Create array using commas as delimiter.
			$oldKeys = explode(',', $clean);
			$newKeys = array();
			
			foreach ($oldKeys as $key)
			{
				// Ignore blank keywords
				if (trim($key)) {
					$newKeys[] = trim($key);
				}
			}

 			// Put array back together, comma delimited.
 			$this->metakey = implode(', ', $newKeys);
		}
	}
	
	public function save($data)
	{
		$timezone = RFApplicationHelper::getUserTimezone();
		if (empty($data['id'])) {
			// save new blackout data 
			
			$resourceIds = array();
			if ($data['all_resources'])
			{
				$scheduleId = $this->getBlackoutScheduleId();
				$resources = $this->resourceRepository->getScheduleResources($scheduleId);
				foreach ($resources as $resource)
				{
					$resourceIds[] = $resource->GetId();
				}
			}
			else
			{
				$resourceIds[] = $data['resource_id'];
			}
			
			$startDate = $data['start_date'];
			$startTime = $data['start_time'];
			$endDate = $data['end_date'];
			$endTime = $data['end_time'];
			
			$blackoutDate = RFDateRange::create($startDate . ' ' . $startTime, $endDate . ' ' . $endTime, $timezone);
			
			$title = $data['title'];
			$conflictAction = $data['conflic_action'];
			
			$repeatType = isset($data['repeat_type']) ? $data['repeat_type'] : 'none';
			$repeatInterval = isset($input['repeat_interval']) ? $input['repeat_interval'] : null;
			$weekDays = isset($data['repeat_days']) ? $data['repeat_days'] : null;
			$monthlyType = isset($data['repeat_monthly_type']) ? $data['repeat_monthly_type'] : 'none';
			
			if (isset($data['repeat_terminated'])) {
				$terminated = RFDate::parse($data['repeat_terminated'], $timezone);
				$terminated->setTime(new RFTime(0, 0, 0, $timezone));
			}
			
			$repeatOptions = JongmanHelper::getRepeatOptions($repeatType, $repeatInterval, $terminated, $weekDays, $monthlyType);
			
			//$repeatOptionsFactory = new RFFactoryRepeatOptions();
			//$repeatOptions = $repeatOptionsFactory->createFromComposite($this->page, $timzeone);
			
			$result = $this->addBlackout($blackoutDate, $resourceIds, $title, RFReservationConflictResolution::create($conflictAction), $repeatOptions);		

			if ($result->wasSuccessful()) {
				return true;
			}else{
				$this->setError($result->message());
				return false;
			}
		}else{
			// update existing blackout data
			
			$id = $data['instance_id'];
			$scope = $data['update_scope'];
			
			$startDate = $data['start_date'];
			$startTime = $data['start_time'];
			$endDate = $data['end_date'];
			$endTime = $data['end_time'];
			$blackoutDate = RFDateRange::create($startDate . ' ' . $startTime, $endDate . ' ' . $endTime, $timezon);
			
			$title = $data['title'];
			$conflictAction = $data['conflict_action'];
			
			$repeatOptionsFactory = new RepeatOptionsFactory();
			$repeatOptions = $repeatOptionsFactory->CreateFromComposite($this->page, $session->Timezone);
			
			$result = $updateBlackout($id, $blackoutDate, $resourceIds, $title, ReservationConflictResolution::create($conflictAction), $repeatOptions, $scope);
			
			//$this->page->ShowUpdateResult($result->WasSuccessful(), $result->Message(), $result->ConflictingReservations(), $result->ConflictingBlackouts(), $session->Timezone);
			if ($result->wasSuccessful()) {
				return true;
			}else{
				$this->setError($result->message());
				return false;
			}	
		}	
	}

	public function delete(&$pks)
	{
		$id = $pks[0];
		//$scope = ;
		
		//Log::Debug('Deleting blackout. BlackoutId=%s, DeleteScope=%s', $id, $scope);
		
		$this->eleteBlackout($id, $scope);		
	}
	/**
	 * Save new blackout series to database (this is method of Manage Blackout Service)
	 * @param RFDateRange $blackoutDate
	 * @param unknown $resourceIds
	 * @param unknown $title
	 * @param IReservationConflictResolution $reservationConflictResolution
	 * @param IRepeatOptions $repeatOptions
	 * @return RFBlackoutDateTimeValidationResult|RFBlackoutValidationResult
	 */
	protected function addBlackout(RFDateRange $blackoutDate, $resourceIds, $title, IReservationConflictResolution $reservationConflictResolution, IRepeatOptions $repeatOptions)
	{
		if (!$blackoutDate->getEnd()->greaterThan($blackoutDate->getBegin()))
		{
			return new RFBlackoutValidationResultDatetime();
		}

		$userId = JFactory::getUser()->get('id');

		$blackoutSeries = RFBlackoutSeries::create($userId, $title, $blackoutDate);
		$blackoutSeries->repeats($repeatOptions);
		

		foreach ($resourceIds as $resourceId)
		{
			$blackoutSeries->addResourceId($resourceId);
		}

		$conflictingBlackouts = $this->getConflictingBlackouts($blackoutSeries);

		$conflictingReservations = array();
		if (empty($conflictingBlackouts))
		{
			$conflictingReservations = $this->getConflictingReservations($blackoutSeries, $reservationConflictResolution);
		}

		$blackoutValidationResult = new RFBlackoutValidationResult($conflictingBlackouts, $conflictingReservations);

		if ($blackoutValidationResult->canBeSaved())
		{
			$this->blackoutRepository->add($blackoutSeries);
		}

		return $blackoutValidationResult;
	}
	
	protected function updateBlackout($blackoutInstanceId, RFDateRange $blackoutDate, $resourceIds, $title, IReservationConflictResolution $reservationConflictResolution, IRepeatOptions $repeatOptions, $scope)
	{
		if (!$blackoutDate->getEnd()->greaterThan($blackoutDate->getBegin()))
		{
			return new RFBlackoutDateTimeValidationResult();
		}
	
		$userId = JFactory::getUser()->get('id');
	
		$blackoutSeries = $this->loadBlackout($blackoutInstanceId, $userId);
	
		if ($blackoutSeries == null)
		{
			return new RFBlackoutSecurityValidationResult();
		}
	
		$blackoutSeries->update($userId, $scope, $title, $blackoutDate, $repeatOptions, $resourceIds);
	
		$conflictingBlackouts = $this->getConflictingBlackouts($blackoutSeries);
	
		$conflictingReservations = array();
		if (empty($conflictingBlackouts))
		{
			$conflictingReservations = $this->getConflictingReservations($blackoutSeries, $reservationConflictResolution);
		}
	
		$blackoutValidationResult = new RFBlackoutValidationResult($conflictingBlackouts, $conflictingReservations);
	
		if ($blackoutValidationResult->canBeSaved())
		{
			$this->blackoutRepository->update($blackoutSeries);
		}
	
		return $blackoutValidationResult;
	}
	
	protected function deleteBlackout($blackoutId, $updateScope)
	{
		if ($updateScope == RFSeriesUpdateScope::FullSeries)
		{
			$this->blackoutRepository->deleteSeries($blackoutId);
		}
		else
		{
			$this->blackoutRepository->delete($blackoutId);
		}
	}
	
	
	/**
	 * @param RFBlackoutSeries $blackoutSeries
	 * @param IReservationConflictResolution $reservationConflictResolution
	 * @return array|RFReservationItemView[]
	 */
	private function getConflictingReservations($blackoutSeries, $reservationConflictResolution)
	{
		$conflictingReservations = array();
	
		$blackouts = $blackoutSeries->allBlackouts();
		foreach ($blackouts as $blackout)
		{
			$existingReservations = $this->reservationViewRepository->getReservationList($blackout->startDate(), $blackout->endDate());
	
			foreach ($existingReservations as $existingReservation)
			{
				if ($blackoutSeries->containsResource($existingReservation->resourceId) && $blackout->date()->overlaps($existingReservation->date))
				{
					if (!$reservationConflictResolution->handle($existingReservation))
					{
						$conflictingReservations[] = $existingReservation;
					}
				}
			}
		}
	
		return $conflictingReservations;
	}
	
	/**
	 * @param RFBlackoutSeries $blackoutSeries
	 * @return array|RFBlackoutItemView[]
	 */
	private function getConflictingBlackouts($blackoutSeries)
	{
		$conflictingBlackouts = array();
	
		$blackouts = $blackoutSeries->allBlackouts();
		foreach ($blackouts as $blackout)
		{
			$existingBlackouts = $this->reservationViewRepository->getBlackoutsWithin($blackout->Date());
	
			foreach ($existingBlackouts as $existingBlackout)
			{
				if ($existingBlackout->seriesId == $blackoutSeries->Id())
				{
					continue;
				}
	
				if ($blackoutSeries->containsResource($existingBlackout->resourceId) && $blackout->date()->overlaps($existingBlackout->date))
				{
	
					$conflictingBlackouts[] = $existingBlackout;
				}
			}
		}
	
		return $conflictingBlackouts;
	}
}