<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

// No direct access
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

class JFormFieldJongmanRepeat extends JFormField
{
	protected $type = 'JongmanRepeat';

	public function getInput() 
	{
		$html = array();
		$html[] = '<div class="jm-recur-container">';
		$html[] = '<label>'.JText::_("COM_JONGMAN_REPEAT").'</label>';
	    $html[] = JHtml::_('select.integerlist', 1, 10, 1, $this->name.'[frequency]');
	    $html[] = JHtml::_('select.genericlist', $this->getIntervalOptions(), $this->name.'[interval]', 'onchange=toggleDays(this)' );
	    
	   	$html[] = '		<div id="repeat_until_div" style="position: relative; visibility: hidden; overflow: show; display: none;">';
		$html[] = '		<label>'.JText::_('COM_JONGMAN_REPEAT_UNTIL_DATE').'</label>';
        $html[] = '		</div>';
        $html[]	= '</div>';
		$jsUrl = JUri::root(true).'/media/com_jongman/js/reservation.js';
		JFactory::getDocument()->addScript($jsUrl);
		return implode($html);
	}
	
	private function getIntervalOptions()
	{
		$options = array(
			JHtml::_("select.option", "none", JText::_("COM_JONGMAN_NONE")),
            JHtml::_("select.option", "daily", JText::_("COM_JONGMAN_DAILY")),
            JHtml::_("select.option", "weekly", JText::_("COM_JONGMAN_WEEKLY")),
            JHtml::_("select.option", "monthly", JText::_("COM_JONGMAN_MONTHLY")),
            JHtml::_("select.option", "yearly", JText::_("COM_JONGMAN_YEARLY"))
          );

		return $options;
	}
	
	private function getWeeknumberOptions()
	{
		$options = array(
			JHtml::_("select.option", "1", JText::_("COM_JONGMAN_FIRST_DAYS")),
	    	JHtml::_("select.option", "2", JText::_("COM_JONGMAN_SECOND_DAYS")),
	    	JHtml::_("select.option", "3", JText::_("COM_JONGMAN_THIRD_DAYS")),
	    	JHtml::_("select.option", "4", JText::_("COM_JONGMAN_FOURTH_DAYS")),
	    	JHtml::_("select.option", "last", JText::_("COM_JONGMAN_LAST_DAYS"))
	    );

		return $options;

	}
}
