<?php
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 *  view.
 *
 * @package     
 * @subpackage  
 * @since       1.0
 */
class JongmanViewCalendar extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;
	
	protected $calendarType = 'month';
	protected $calendar;
	
	protected $months = array(0=>'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE', 'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER');
	protected $days = array(0=>'MONDAY', 'TUESDAY', 'WEDNESDAY', 'THURSDAY', 'FRIDAY', 'SATURDAY', 'SUNDAY');
	/**
	 * Prepare and display the calendar view.
	 *
	 * @return  void
	 * @since   3.0
	 */
	public function display($tp = NULL)
	{
		// Initialise variables.
		$this->state		= $this->get('State');

		$this->calendar 	= $this->get('Calendar'); 
		$this->firstDay 	= $this->get('FirstDay');
		$this->displayDate 	= $this->calendar->firstDay();
		$this->filters 		= $this->get('Filters');
		$this->resourceId	= $this->get('ResourceId');
		$this->scheduleId	= $this->get('ScheduleId');
		$prev 				= $this->calendar->getPreviousDate();
		$next 				= $this->calendar->getNextDate();
		$calendarType 		= $this->calendar->getType();
		
		$this->prevLink = RFCalendarUrl::create($prev, $calendarType);
		$this->nextLink = RFCalendarUrl::create($next, $calendarType);
		
		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}
		//$mode = '-testing';
		$mode = '';
		JHtml::_('script', 'com_jongman/fullcalendar'.$mode.'/moment.min.js', false, true);		
		JHtml::_('script', 'com_jongman/jongman/utils.js', false, true);
		JHtml::_('script', 'com_jongman/jongman/reservation-popup.js', false, true);
		JHtml::_('script', 'com_jongman/jquery/jquery.ui.dialog.js', false, true);
		JHtml::_('script', 'com_jongman/jquery/jquery.dialog-options.js', false, true);
		JHtml::_('stylesheet', 'com_jongman/fullcalendar'.$mode.'/fullcalendar.css', false, true, false, false, false);
		//JHtml::_('stylesheet', 'com_jongman/fullcalendar'.$mode.'/fullcalendar.print.css', false, true, false, false, false);
		JHtml::_('stylesheet', 'com_jongman/fullcalendar'.$mode.'/calendar.css', false, true, false, false, false);
		JHtml::_('stylesheet', 'com_jongman/jongman/jongman.css', false, true, false, false, false);
		JHtml::_('stylesheet', 'com_jongman/jongman/schedule.css', false, true, false, false, false);
		JHtml::_('stylesheet', 'com_jongman/jquery/jquery-ui.css', false, true, false, false, false);
		if ($mode == '-testing') { 
			JHtml::_('script', 'com_jongman/fullcalendar'.$mode.'/fullcalendar.js', false, true);
		}else{
			JHtml::_('script', 'com_jongman/fullcalendar'.$mode.'/fullcalendar.min.js', false, true);
		}
		JHtml::_('script', 'com_jongman/jongman/calendar'.$mode.'.js', false, true);
		// Display the view layout.
		parent::display($tp);
	}


}