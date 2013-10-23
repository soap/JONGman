<?php
defined('_JEXEC') or die;
/**
 * 
 * Pagination/Navigation class for JONGman schedule
 * @author Prasit Gebsaap
 * @since version 2.0
 */
class JongmanNavigator extends JObject 
{
	public $firstDayTs = null;
	
	public $viewDays = null;
	
	private $_printAllCols = false;
	
	/**
	 * 
	 * JONGman schedule navigator object
	 * @param int $firstDayTs first day time stamp
	 * @param int $viewDays number of day to display (max is 7)
	 */
	public function __construct($firstDayTs, $viewDays) 
	{
		$this->firstDayTs = $firstDayTs;
		$this->viewDays = $viewDays;
		$this->_printAllCols = ($this->viewDays != 7);	
	}

	protected function getUrl()
	{
		$url = JSite::getMenu()->getActive()->link;
		return $url;    	
	}
	
	protected function getDateVars()
	{
		$date = getdate($this->firstDayTs);
		return $date;		
	}
	
	protected function getPreviousWeekLink()
	{
		$url = $this->getUrl();
		$date = $this->getDateVars();
        $m = $date['mon'];
        $d = $date['mday'];
        $y = $date['year'];
		$url = JRoute::_( $url.'&date='.date('Y-m-d',mktime(0,0,0,$m, $d - 7, $y)));
		$title = JText::_("COM_JONGMAN_PREV_WEEK");
		return "<a href=\"$url\">$title</a>";		
	}
	
	protected function getThisWeekLink() 
	{
		$url = $this->getUrl();
		$url = JRoute::_( $url );
		$title = JText::_($this->viewDays == 1?"COM_JONGMAN_TODAY":"COM_JONGMAN_THIS_WEEK");
		return "<a href=\"$url\">$title</a>";
	}
	
	protected function getNextWeekLink() 
	{
		$url = $this->getUrl();
		$date = $this->getDateVars();
        $m = $date['mon'];
        $d = $date['mday'];
        $y = $date['year'];
        
		$url = JRoute::_( $url.'&date='.date('Y-m-d',mktime(0,0,0,$m, $d + 7, $y)));
		$title = JText::_("COM_JONGMAN_NEXT_WEEK");
		return "<a href=\"$url\">$title</a>";	
	}
	
	protected function getPreviousDaysLink()
	{
		$url = $this->getUrl();
		$date = $this->getDateVars();
        $m = $date['mon'];
        $d = $date['mday'];
        $y = $date['year'];
        
		$url = JRoute::_( $url.'&date='.date('Y-m-d',mktime(0,0,0,$m, $d - $this->viewDays, $y)));
		$title = JText::plural("COM_JONGMAN_PREV_N_DAYS", $this->viewDays);
		return "<a href=\"$url\">$title</a>";			
	}
	
	protected function getNextDaysLink()
	{
		$url = $this->getUrl();
		$date = $this->getDateVars();
        $m = $date['mon'];
        $d = $date['mday'];
        $y = $date['year'];
        
		$url = JRoute::_( $url.'&date='.date('Y-m-d',mktime(0,0,0,$m, $d + $this->viewDays, $y)));
		$title = JText::plural("COM_JONGMAN_NEXT_N_DAYS", $this->viewDays);
		return "<a href=\"$url\">$title</a>";		
	}
	
	public function getListFooter()
	{
		$list = array();
		$list['prevWeekLink']	= $this->getPreviousWeekLink();
		$list['prevDaysLink']	= $this->getPreviousDaysLink();
		$list['thisWeekLink'] 	= $this->getThisWeekLink();
		$list['nextDaysLink']	= $this->getNextDaysLink();
		$list['nextWeekLink'] = $this->getNextWeekLink();
		
		$app = JFactory::getApplication();	
		$chromePath = JPATH_THEMES . '/' . $app->getTemplate() . '/html/com_jongman/navigator.php';
		if (file_exists($chromePath))
		{
			include_once $chromePath;
			if (function_exists('pagination_list_footer'))
			{
				return pagination_list_footer($list);
			}
		}
		return $this->_list_footer($list);	
	}
	
	protected function _list_footer($list) 
	{
		$html = "<div class=\"jm-pagination\">\n";
		if ($this->viewDays != 1) {  
        	$html .= "<span class=\"prev\">".$list['prevWeekLink']."</span>\n";
		}
        if ($this->_printAllCols) {
        	$html .= "<span class=\"prev\">".$list['prevDaysLink']."</span>\n";	
        }
        $html .= "<span class=\"current\">".$list['thisWeekLink']."</span>\n";
	    if ($this->_printAllCols) {
        	$html .= "<span class=\"next\">".$list['nextDaysLink']."</span>\n";	
        }
        if ($this->viewDays != 1) {
        	$html .= "<span class=\"next\">".$list['nextWeekLink']."</span>\n";	
        }
        
    	$html .= "</div>\n";
    	
    	return $html;	
	
	}
}