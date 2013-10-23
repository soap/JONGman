<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

/**
* Formats and truncates reservation summaries for display on the schedule
*/

class JongmanSummary
{
	private $size;
	
	protected $visible = false;
	protected $userNname = '';
    protected $title = '';
	protected $text = '';
	protected $cellDisplay = 'userOnly'; //titleOnly, userAndTitle, none
	
	public $EMPTY_SUMMARY = '&nbsp;';
	
	function __construct($config=array()) {
		if (array_key_exists('text', $config)) {
			$this->text = htmlspecialchars($config['text']); //reservation description (detail)
		}
		
		if (array_key_exists('userName', $config)) {
			$this->userName= $config['userName'];	//reservation owner
		}
		
		if (array_key_exists('title', $config)) {
			$this->title = htmlspecialchars($config['title']); //reservation title
		}
		
		if (array_key_exists('cellDisplay', $config)) {
			$this->cellDisplay = $config['cellDisplay'];
		}
		
		$this->size = JComponentHelper::getParams('com_jongman')->get('reservationBarTextLength'); 
	}
	
	function toScheduleCell($available_chars = -1) {
		$summary = $this->EMPTY_SUMMARY;
		$length = iconv_strlen($this->userName, 'UTF-8');
		
		if ($available_chars == -1 || $available_chars > $this->getSize()) {
			$available_chars = $this->getSize();
		}
		
		if ($this->isVisible()) {
			if ($this->cellDisplay == 'userOnly') {
				if (!empty($this->userName) && strlen($this->userName) >= $available_chars) {
					$summary = iconv_substr($this->userName, 0, $available_chars, 'UTF-8');
					$length = iconv_strlen($this->userName, 'UTF-8');
				}
			}
			
			if ($this->cellDisplay == 'userAndTitle') {
				if (!empty($this->userName) && $this->getSize() >= $available_chars) {
					$summary = "{$this->userName}";
					$length = iconv_strlen($this->userName, 'UTF-8');
				}
				
				if (!empty($this->title) && $this->getSize() >= $available_chars) {
					$summary .= '/<i>' .iconv_substr($this->title, 0, $available_chars - 1 - iconv_strlen($this->userName, 'UTF-8'), 'UTF-8') . '</i>';
					$length += (iconv_strlen($this->title, 'UTF-8') + 1);	
				}
				
			}
			
			if ($this->cellDisplay == 'titleOnly') {
				if (!empty($this->title) && $this->getSize() >= $available_chars) {
					$summary = iconv_substr($this->title, 0, $available_chars, 'UTF-8');
					$length =  iconv_strlen($this->title, 'UTF-8');
				}
			}
			
			/** for none, nothing to do */
			if ( $available_chars < $length ) {
				$summary .= '...';
			}
		}
		
		return $summary;
	}
	
	/**
     * Prepare text for Mootools tooltip
     * @note Mootools tooltip limit text to 50 characters
     */
	function toScheduleHover() {
		if ((bool)JComponentHelper::getParams('com_jongman')->get('disableSummary')) {
			return $this->EMPTY_SUMMARY;
		}
		
		if (!empty($this->title)) {
			return "{$this->title}::" .iconv_substr($this->text, 0, 50, 'UTF-8') ;
		}
		else {
			return "(NA)::" . iconv_substr($this->text, 0, 50, 'UTF-8');
		}		
	}
	
	function getSize() {
		return $this->size;
	}
	
	function isVisible() {
		return $this->visible &&  ($this->getSize() > 0);
	}
	
	function setVisible($value) {
		$this->visible = (bool)$value;
	}
	
	function getTitle() {
		return $this->title;
	}
}
?>