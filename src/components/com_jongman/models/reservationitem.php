<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;
jimport('joomla.application.component.modelitem');
/**
 * The Jongman ResourceForm model extends from backend Resource model.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       1.0
 */
class JongmanModelReservationitem extends JModelItem
{
	
	protected $_forms = array();
	/**
	 * Method to auto-populate the model state.
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return    void
	 */
	protected function populateState()
	{
		// Load state from the request.
		$pk = JFactory::getApplication()->input->getInt('id');
		$this->setState($this->getName() . '.id', $pk);
	
		// Load the parameters.
		$params = JFactory::getApplication('site')->getParams();
		$this->setState('params', $params);
	
		$access = JongmanHelper::getActions();
		if (!$access->get('core.edit.state') && !$access->get('core.edit')) {
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}
	}
	
	/**
	 * Method to get a form object.
	 *
	 * @param   string   $name     The name of the form.
	 * @param   string   $source   The form source. Can be XML string if file flag is set to false.
	 * @param   array    $options  Optional array of options for the form creation.
	 * @param   boolean  $clear    Optional argument to force load a new form.
	 * @param   string   $xpath    An optional xpath to search for the fields.
	 *
	 * @return  mixed  JForm object on success, False on error.
	 *
	 * @see     JForm
	 * @since   12.2
	 */
	protected function loadForm($name, $source = null, $options = array(), $clear = false, $xpath = false)
	{
		// Handle the optional arguments.
		$options['control'] = JArrayHelper::getValue($options, 'control', false);
	
		// Create a signature hash.
		$hash = md5($source . serialize($options));
	
		// Check if we can use a previously loaded form.
		if (isset($this->_forms[$hash]) && !$clear)
		{
			return $this->_forms[$hash];
		}
	
		// Get the form.
		JForm::addFormPath(JPATH_COMPONENT . '/models/forms');
		JForm::addFieldPath(JPATH_COMPONENT . '/models/fields');
		JForm::addFormPath(JPATH_COMPONENT . '/model/form');
		JForm::addFieldPath(JPATH_COMPONENT . '/model/field');
	
		try
		{
			$form = JForm::getInstance($name, $source, $options, false, $xpath);
	
			if (isset($options['load_data']) && $options['load_data'])
			{
				// Get the data for the form.
				$data = $this->loadFormData();
			}
			else
			{
				$data = array();
			}
	
			// Allow for additional modification of the form, and events to be triggered.
			// We pass the data because plugins may require it.
			$this->preprocessForm($form, $data);
	
			// Load the data into the form after the plugins have operated.
			$form->bind($data);
		}
		catch (Exception $e)
		{
			$this->setError($e->getMessage());
	
			return false;
		}
	
		// Store the form for later.
		$this->_forms[$hash] = $form;
	
		return $form;
	}
	
	/**
	 * Method to get the Custom form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 * @since   1.0
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
		
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin('extension');
		$dispatcher = JEventDispatcher::getInstance();
		
		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onReservationSeriesPrepareForm', array($form, $data));
		
		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();
		
			if (!($error instanceof Exception))
			{
				throw new Exception($error);
			}
		}
	
		return $form;
	}
	
	
	protected function loadFormData()
	{
		$data = $this->getItem();
		return $data;
	}
	
	protected function preprocessForm(JForm $form, $data, $group = 'extension')
	{
		// Import the appropriate plugin group.
		JPluginHelper::importPlugin($group);
		$dispatcher = JEventDispatcher::getInstance();
	
		// Trigger the form preparation event.
		$results = $dispatcher->trigger('onReservationSeriesPrepareForm', array($form, $data));
	
		// Check for errors encountered while preparing the form.
		if (count($results) && in_array(false, $results, true))
		{
			// Get the last error.
			$error = $dispatcher->getError();
	
			if (!($error instanceof Exception))
			{
				throw new Exception($error);
			}
		}	
		
		$fieldSets = $form->getFieldsets('reservation_custom_fields');
		foreach ($fieldSets as $fieldSet) {
			foreach ($form->getFieldset($fieldSet->name) as $field) {
				$form->setFieldAttribute($field->fieldname, 'readonly', 'true');	
				$form->setFieldAttribute($field->fieldname, 'disabled', 'disabled');
				$form->setFieldAttribute($field->fieldname, 'class', $field->class.' uneditable-input');
			}
		}
	}
	
	public function getItem($referenceNumber=null)
	{
		if (empty($referenceNumber)) {
			$pk = $this->getState($this->getName() . '.id');
			if (!empty($pk)) {
				$table = JTable::getInstance('Instance', 'JongmanTable');
				$table->load($pk);
				$referenceNumber = $table->reference_number;
			}else{
				$referenceNumber = JFactory::getApplication()->input->getCmd('ref', null);
				if (empty($referenceNumber)) {
					JError::raiseWarning(500, 'No primary key provided');	
				}
			} 
		}

		/* This $pk is an instance id */
		if (!empty($this->_item[$referenceNumber])) return $this->_item[$referenceNumber];

		
		$query = $this->_db->getQuery(true);
		$query->select('a.id, a.start_date, a.end_date, a.reference_number, a.reservation_id')
			->from('#__jongman_reservation_instances AS a');
			
		$query->select('r.title, r.customer_id, r.created, r.created_by, r.description, r.owner_id, r.access, r.attribs')
			->join('INNER', '#__jongman_reservations AS r ON r.id=a.reservation_id');
			
		$query->select('own.name as owner_name')
			->join('LEFT', '#__users AS own ON own.id=r.owner_id');
		
		$query->select('ua.name as author')
			->join('LEFT', '#__users AS ua ON ua.id=r.created_by');

		$query->where('a.reference_number='.$this->_db->quote($referenceNumber));
		
		$this->_db->setQuery($query);
		
		$item = $this->_db->loadObject();
		
		$item->users = array();
		$item->params = new JRegistry();
		$item->attribs = new JRegistry($item->attribs);
		$item->resources = $this->getResources($item->reservation_id);
		$item->customer = $this->getCustomer($item->customer_id);
		
		// Compute selected asset permissions.
		$user   = JFactory::getUser();
		$uid    = $user->get('id');
		
		$app = JFactory::getApplication();
		$params = $app->getParams();
		
		$workflow = (bool)($params->get('approvalSystem') == 2);
		if ($workflow) {
			jimport('workflow.framework');
			if (WFApplicationHelper::isWorkflowEnabled('com_jongman.reservation', $item->reservation_id)) {
				$item->workflow_enabled = true;
				$item->workflow_state = WFApplicationHelper::getStateByContext('com_jongman.reservation', $item->reservation_id);
			}else{
				$item->workflow_enabled = false;
				$item->workflow_state = new StdClass();
			}
		}else{
			$item->workflow_enabled = false;
			$item->workflow_state = new StdClass();
		}
		
		if (!$item->workflow_enabled) {
			$access = JongmanHelper::getActions($item->reservation_id);
		}else{
			$access = WFApplicationHelper::getActions('com_jongman.reservation', $item->reservation_id);	
		}
		
		$view_access = true;
		
		if ($item->access && !$user->authorise('core.admin')) {
			$view_access = in_array($item->access, $user->getAuthorisedViewLevels());
		}
		
		$item->params->set('access-view', $view_access);
		
		if (!$view_access) {
			$item->params->set('access-edit', false);
			$item->params->set('access-change', false);
		}
		else {
			// Check general edit permission first.
			if ($access->get('core.edit')) {
				$item->params->set('access-edit', true);
			}
			elseif (!empty($uid) &&  $access->get('core.edit.own')) {
				// Check for a valid user and that they are the owner.
				if (($uid == $item->created_by) || ($uid == $item->owner_id)) {
					$item->params->set('access-edit', true);
				}
			}
		
			// Check edit state permission.
			$item->params->set('access-change', $access->get('core.edit.state'));
		}
		
		if (!$view_access) {
			$item->params->set('access-delete', false);
		}
		else {
			// Check general edit permission first.
			if ($access->get('core.delete')) {
				$item->params->set('access-delete', true);
			}
			elseif (!empty($uid) &&  $access->get('core.delete.own')) {
				// Check for a valid user and that they are the owner.
				if (($uid == $item->created_by) || ($uid == $item->owner_id)) {
					$item->params->set('access-delete', true);
				}
			}
		}
		
		$dispatcher = JEventDispatcher::getInstance();
		JPluginHelper::importPlugin('extension');
		$results = $dispatcher->trigger('onReservationSeriesPrepareData', array('com_jongman.reservationitem', $item));
		
		if (count($results) && in_array(false, $results, true)) {
		 	$this->setError($dispatcher->getError());
		 	return false;
		}
		
		$this->_item[$referenceNumber] = $item;
		return $this->_item[$referenceNumber];			
	}
	
	public function getResources($reservationId=null)
	{
		if (empty($reservationId)) {
			$item = $this->getItem();
			$reservationId = $item->reservation_id;	
		}
		
		$query = $this->_db->getQuery(true);
		$query->select('a.resource_id, a.resource_level')
			->from('#__jongman_reservation_resources AS a')
			->select('r.title as resource_title')
			->join('inner', '#__jongman_resources AS r ON r.id=a.resource_id');
			
		$query->where('a.reservation_id='.$reservationId);
		$query->order('a.resource_level ASC');
		
		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
				
	}
	
	public function getCustomer($customerId = null) 
	{
		if ($customerId === null) {
			return false;
		}
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__jongman_customers')
			->where('id ='.$customerId);
		
		$db->setQuery($query);
		$customer = $db->loadObject();
		
		return $customer;
	}
	/**
	 * 
	 * @return Ambigous <multitype:, string>
	 * @since 3.0
	 */
	public function getTransitions()
	{
		$item = $this->getItem();
		
		if (!$item->workflow_enabled) return array();
		
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'components/com_workflow/tables');
		$wfInstance = JTable::getInstance('Instance', 'WorkflowTable');
		$wfInstance->load(array('context'=>'com_jongman.reservation', 'item_id'=>$item->reservation_id));
		
		$oUser = JFactory::getUser();
		$transitions = WFApplicationHelper::getTransitionsForInstanceUser($wfInstance, $oUser, true);
		
		return $transitions;
	}
	
	/**
	 * 
	 * @return Ambigous <multitype:, mixed>
	 * @since 3.0
	 */
	public function getLogs()
	{
		$item = $this->getItem();
		if (!$item->workflow_enabled) return array();
		return WFApplicationHelper::getTransitionLogs('com_jongman.reservation', $item->reservation_id);
	}
}