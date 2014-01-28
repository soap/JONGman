<?php
defined('_JEXEC') or die;

/**
 * 
 * Pagination/Navigation class for JONGman schedule
 * @author Prasit Gebsaap
 * @since version 2.0
 */
class RFNavigator extends JObject 
{	
	private $start_date;
	private $end_date;
	private $view_days;
	
	public function __construct($startDate, $endDate, $viewDays ) 
	{
		$this->start_date = $startDate;
		$this->end_date = $endDate;
		$this->view_days = $viewDays;
	}

	protected function getUrl()
	{
		$url = JSite::getMenu()->getActive()->link;
		return $url;    	
	}
	
	protected function getPreviousWeekLink()
	{
		$url = $this->getUrl().'&sd='.$this->start_date->getDate()->format('Y-m-d').'&Itemid='.JSite::getMenu()->getActive()->id;
		$html = "<a href=\"{$url}\">".JText::_('COM_JONGMAN_PREV_WEEK')."</a>";
		
		return $html;
	}
	
	protected function getThisWeekLink() 
	{
		$url = $this->getUrl().'&Itemid='.JSite::getMenu()->getActive()->id;
		$html = "<a href=\"{$url}\">".JText::_('COM_JONGMAN_THIS_WEEK')."</a>";
		
		return $html;
	}
	
	protected function getNextWeekLink() 
	{
		$url = $this->getUrl().'&sd='.$this->end_date->getDate()->format('Y-m-d').'&Itemid='.JSite::getMenu()->getActive()->id;
		$html = "<a href=\"{$url}\">".JText::_('COM_JONGMAN_NEXT_WEEK')."</a>";
		
		return $html;
	}
	
	
	public function getListFooter()
	{
		$list = array();
		$list['prevWeekLink']	= $this->getPreviousWeekLink();
		$list['thisWeekLink'] 	= $this->getThisWeekLink();
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
		if ($this->view_days != 1) {  
        	$html .= "<span class=\"prev\">".$list['prevWeekLink']."</span>\n";
		}

        $html .= "<span class=\"current\">".$list['thisWeekLink']."</span>\n";

        if ($this->view_days != 1) {
        	$html .= "<span class=\"next\">".$list['nextWeekLink']."</span>\n";	
        }
        
    	$html .= "</div>\n";
    	
    	return $html;	
	
	}
}