<?php
defined('_JEXEC') or die;
jimport('joomla.database.table');

/**
 * Layout table.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanTableLayout extends JTable
{
	/**
	 * Constructor.
	 *
	 * @param   JDatabase  $db  A database connector object.
	 *
	 * @return  JongmanTableLayout
	 * @since   2.0
	 */
	public function __construct($db)
	{
		parent::__construct('#__jongman_layouts', 'id', $db);
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
		// Check for valid name.
		if (trim($this->title) === '') {
			$this->setError(JText::_('COM_JONGMAN_ERROR_LAYOUT_TITLE'));
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
		
		// Verify if there is default layout
		if ($this->default == '0' && $this->published == '1'){
			$table = JTable::getInstance('Layout', 'JongmanTable');
			if (!$table->load( array('default' => '1')) ) {
				$this->default = '1';	
			}				
		} 
		
		// Verify that the default layout is unique
		if ($this->default == '1') {		
			$table = JTable::getInstance('Layout', 'JongmanTable');
			if ($table->load( array('default' => '1') ))
			{
				if ($table->checked_out && $table->checked_out != $this->checked_out)
				{
					$this->setError(JText::_('COM_JONGMAN_ERROR_LAYOUT_DEFAULT_CHECKIN_USER_MISMATCH'));
					return false;
				}
				$table->default = 0;
				$table->checked_out = 0;
				$table->checked_out_time = $this->_db->getNullDate();
				$table->store();
			}
			
			// Verify that the default for this layout is unique.
			if ($table->load(array('default' => '1')) && ($table->id != $this->id || $this->id == 0))
			{
				$this->setError(JText::_('COM_JONGMAN_DATABASE_ERROR_LAYOUT_DEFAULT_NOT_UNIQUE'));
				return false;
			}
		}

		// Attempt to store the data.
		return parent::store($updateNulls);
	}
	
	public function delete($pk = null)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// If no primary key is given, return false.
		if ($pk === null)
		{
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_NULL_PRIMARY_KEY'));
			$this->setError($e);
			return false;
		}
		
		$table = JTable::getInstance('Layout', 'JongmanTable');
		if ($table->load($pk)) {
			if ($table->default == '1') {
				$this->setError('COM_JONGMAN_ERROR_LAYOUT_DEFAULT_DELETE');	
				return false;
			}		
		}else{
			$this->setError('COM_JONGMAN_DATABASE_RECORD_NOT_FOUND');
			return false;
		}
		
		return parent::delete($pk);
	}
}