<?php
defined('_JEXEC') or die;
// import dependancies.
jimport('joomla.application.component.view');


/**
 * Jongman view.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       2.0
 */
class JongmanViewSchedule extends JViewLegacy
{
	/**
	 * @var    record of schedule
	 * @since  1.0
	 */
	protected $schedule;
	protected $datevars;
	protected $resources;
	protected $reservations;
	
	protected $scheduledates;
	/**
	 * 
	 * Daily layout for this schedule
	 * @var unknown_type
	 */
	protected $layout;
	/**
	 * @var    The pagination object for the list.
	 * @since  1.0
	 */
	protected $navigation;

	/**
	 * @var    JObject	The model state.
	 * @since  1.0
	 */
	protected $state;

	/**
	 * Prepare and display the schedule reservation view.
	 *
	 * @return  void
	 * @since   1.0
	 */
	public function display($tp = null)
	{
		if ($this->getLayout()=='error') {
			parent::display($tp);
			return true;
		}
		// Initialise variables.
		$this->schedule			= $this->get('Item');
		$this->resources		= $this->get("Resources");
		//$this->reservations 	= $this->get("Reservations");
		/**
		 * @since 2.0
		 */
		$this->layout			= $this->get("DailyLayout");
		$this->scheduledates	= $this->get("ScheduleDates");
		$this->navLinks  		= $this->get("NavigationLinks");
		//-------------------------------------------------------
		$this->navigation	= $this->get('Navigation');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		parent::display($tp);
	}

	/**
	 * get toolbar HTML.
	 *
	 * @return  void
	 * @since   1.0
	 */
	protected function getToolbar()
	{
		
	}
}