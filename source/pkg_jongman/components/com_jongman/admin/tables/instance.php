<?php
defined('_JEXEC') or die;
jimport('joomla.database.table');

/**
 * Instance table.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanTableInstance extends JTable
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabase  $db  A database connector object.
	 *
	 * @return  JongmanTableInstance
	 * @since   2.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__jongman_reservation_instances', 'id', $db);
	}

	/**
	 * Overloaded bind function to pre-process the params.
	 *
	 * @param   array   $array   The input array to bind.
	 * @param   string  $ignore  A list of fields to ignore in the binding.
	 *
	 * @return  null|string	null is operation was satisfactory, otherwise returns an error
	 * @see     JTable:bind
	 * @since   2.0
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
	 * @since   2.0
	 */
	public function check()
	{
		// Check for valid reference number.
		if (trim($this->reference_number) === '') {
			$this->setError(JText::_('COM_JONGMAN_ERROR_INSTANCE_REFERNECE_NUMBER'));
			return false;
		}
		
		// Check for valid reservation id.
		if (empty($this->reservation_id)) {
			$this->setError(JText::_('COM_JONGMAN_ERROR_INSTANCE_RESERVATION_ID'));
			return false;
		}		

		// Check for valid start_date
		if (trim($this->start_date) === '') {
			$this->setError(JText::_('COM_JONGMAN_ERROR_INSTANCE_START_DATE'));
			return false;
		}			
		
		// Check for valid end date.
		if (empty($this->end_date)) {
			$this->setError(JText::_('COM_JONGMAN_ERROR_INSTANCE_END_DATE'));
			return false;
		}	
		return true;
	}

	/**
	 * Overload the store method for the Weblinks table.
	 *
	 * @param   boolean  $updateNulls  Toggle whether null values should be updated.
	 *
	 * @return  boolean  True on success, false on failure.
	 * @since   2.0
	 */
	public function store($updateNulls = false)
	{
		// Initialiase variables.
		$date	= JFactory::getDate()->toSQL();
		$userId	= JFactory::getUser()->get('id');

		if (empty($this->id)) {
			// New record.
			$this->created		= $date;
			$this->created_by	= $userId;
		} 
		else {
			// Existing record.
			$this->modified	= $date;
			$this->modified_by	= $userId;
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}
}