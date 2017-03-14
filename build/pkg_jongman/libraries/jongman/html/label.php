<?php
defined('_JEXEC') or die;

abstract class RFhtmlLabel
{
	/**
	 * Returns the labels of an item as formatted html
	 *
	 * @param     array     $labels    The labels
	 *
	 * @return    string               The label html
	 */
	public static function labels($labels = null)
	{
		if (!is_array($labels)) {
			return '';
		}

		$html = array();

		foreach ($labels AS $label)
		{
			$style  = ($label->style ? ' ' . $label->style : '');
			$title  = htmlspecialchars($label->title, ENT_COMPAT, 'UTF-8');
			$html[] = '<span class="label' . $style. '"><i class="icon-bookmark"></i> ' . $title . '</span>';
		}

		return implode(' ', $html);
	}


	/**
	 * Returns a date as literal label
	 *
	 * @param     string    $date       The date
	 * @param     string    $compact    If set to true, will only show the amount of days
	 *
	 * @return    string                The label html
	 */
	public static function datetime($date, $compact = false, $options = array())
	{
		static $format = null;
		static $time_offset = null;

		if (is_null($format)) {
			$params = JComponentHelper::getParams('com_jongman');
			$format = $params->get('datetimeFormat');

			if (!$format) {
				$format = JText::_('DATE_FORMAT_LC1');
			}
		}

		if (is_null($time_offset)) {
			$config = JFactory::getConfig();
			$user   = JFactory::getUser();

			$time_offset = $user->getParam('timezone', $config->get('offset'));
		}

		if (!isset($options['tz'])) {
			$options['tz'] = true;
		}

		$string = RFDate::relative($date, $options['tz']);
		if ($string == false) return '';

		if ($options['tz']) {
			// Get a date object based on UTC.
			$dateObj  = JFactory::getDate($date, 'UTC');
			$now_date = JFactory::getDate('now', 'UTC');

			// Set the correct time zone based on the user configuration.
			$dateObj->setTimeZone(new DateTimeZone($time_offset));
			$now_date->setTimeZone(new DateTimeZone($time_offset));

			$timestamp = strtotime($dateObj->calendar('Y-m-d H:i:s', true));
			$now       = strtotime($now_date->format('Y-m-d H:i:s', true, false));
		}
		else {
			$timestamp = strtotime($date);
			$now = time();
		}

		$remaining = $timestamp - $now;
		$is_past   = ($remaining <= 0) ? true : false;
		$tooltip   = JHtml::_('date', $date, $format, ($options['tz'] ? false : true));

		if ($compact) {
			$days   = round($remaining / 86400);

			if ($days == 0) {
				$string = '0';
			}
			else {
				$string = ($is_past ? '' : '+') . $days;
			}
		}

		$past_class   = (isset($options['past-class'])   ? $options['past-class']   : 'label-important');
		$past_icon    = (isset($options['past-icon'])    ? $options['past-icon']    : 'warning');
		$future_class = (isset($options['future-class']) ? $options['future-class'] : 'label-success');
		$future_icon  = (isset($options['future-icon'])  ? $options['future-icon']  : 'calendar');


		$html = array();
		$html[] = '<span class="label ' . ($is_past ? $past_class : $future_class);
		$html[] = ' hasTooltip" rel="tooltip" title="' . $tooltip . '" style="cursor: help">';
		$html[] = '<span aria-hidden="true" class="icon-' . ($is_past ? $past_icon : $future_icon) . '"></span> ';
		$html[] = $string;
		$html[] = '</span>';

		return implode('', $html);
	}

	/**
	 * Returns a daterang as literal label
	 *
	 * @param     string    $date       The date range
	 * @param     string    $compact    If set to true, will only show the amount of days
	 *
	 * @return    string                The label html
	 */
	public static function daterange($startDate, $endDate, $compact = false, $options = array())
	{
		static $format = null;
		static $time_offset = null;
	 
		if (is_null($format)) {
			$params = JComponentHelper::getParams('com_jongman');
			$format = $params->get('datetimeFormat');
	
			if (!$format) {
				$format = JText::_('DATE_FORMAT_LC1');
			}
		}
	
		if (is_null($time_offset)) {
			$config = JFactory::getConfig();
			$user   = JFactory::getUser();
	
			$time_offset = $user->getParam('timezone', $config->get('offset'));
		}
	
		if (!isset($options['tz'])) {
			$options['tz'] = true;
		}
	
		$string = RFDate::relative($startDate, $options['tz']);
		if ($string == false) return '';
	
		if ($options['tz']) {
			// Get a date object based on UTC.
			$dateObj  = JFactory::getDate($startDate, 'UTC');
			$now_date = JFactory::getDate('now', 'UTC');
	
			// Set the correct time zone based on the user configuration.
			$dateObj->setTimeZone(new DateTimeZone($time_offset));
			$now_date->setTimeZone(new DateTimeZone($time_offset));
	
			$timestamp = strtotime($dateObj->calendar('Y-m-d H:i:s', true));
			$now       = strtotime($now_date->format('Y-m-d H:i:s', true, false));
		}
		else {
			$timestamp = strtotime($startDate);
			$now = time();
		}
	
		$remaining = $timestamp - $now;
		$is_past   = ($remaining <= 0) ? true : false;
		$tooltip   = JHtml::_('date', $startDate, $format, ($options['tz'] ? false : true));
		$tooltip  .= ' - '.JHtml::_('date', $endDate, $format, ($options['tz'] ? false : true));		
		if ($compact) {
			$days   = round($remaining / 86400);
	
			if ($days == 0) {
				$string = '0';
			}
			else {
				$string = ($is_past ? '' : '+') . $days;
			}
		}
	
		$past_class   = (isset($options['past-class'])   ? $options['past-class']   : 'label-important');
		$past_icon    = (isset($options['past-icon'])    ? $options['past-icon']    : 'warning');
		$future_class = (isset($options['future-class']) ? $options['future-class'] : 'label-success');
		$future_icon  = (isset($options['future-icon'])  ? $options['future-icon']  : 'calendar');
	
	
		$html = array();
		$html[] = '<span class="label ' . ($is_past ? $past_class : $future_class);
		$html[] = ' hasTooltip" rel="tooltip" title="' . $tooltip . '" style="cursor: help">';
		$html[] = '<span aria-hidden="true" class="icon-' . ($is_past ? $past_icon : $future_icon) . '"></span> ';
		$html[] = $string;
		$html[] = '</span>';
	
		return implode('', $html);
	}

	/**
	 * Returns the author of an item as label
	 *
	 * @param     string    $name      The user name
	 * @param     string    $date      The created date
	 * @param     string    $format    The new date format for the tooltip
	 *
	 * @return    string               The label html
	 */
	public static function author($name = null, $date = null, $format = null)
	{
		if (!$name || !$date) {
			return '';
		}

		$string = RFDate::relative($date);

		if ($string == false) {
			return '';
		}

		$tooltip = $string . '<br />' . JHtml::_('date', $date, ($format ? $format : JText::_('DATE_FORMAT_LC1')));

		$html = array();
		$html[] = '<span class="label label-info hasTooltip" title="' . $tooltip . '" style="cursor: help">';
		$html[] = '<i class="icon-user glyphicon glyphicon-user"></i> ';
		$html[] = htmlspecialchars($name, ENT_COMPAT, 'UTF-8');
		$html[] = '</span>';

		return implode('', $html);
	}

	public static function state($name = null, $date = null, $format = null, $color='')
	{
		if (!$name || !$date) {
			return '';
		}
		$name = trim($name);
		$string = RFDate::relative($date);
	
		if ($string == false) {
			return '';
		}
	
		$tooltip = $string . '<br />' . JHtml::_('date', $date, ($format ? $format : JText::_('DATE_FORMAT_LC1')));
	
		$html = array();
		if (!empty($color)) {
			$bgColor = self::hex2rgb($color);
			$html[] = '<span class="label hasTooltip" title="' . $tooltip . '" style="cursor: help; background-color: rgb('.implode(',', $bgColor).')">';
		}else{
			$html[] = '<span class="label label-info hasTooltip" title="' . $tooltip . '" style="cursor: help">';
		}
		
		//$html[] = '<i class="icon-tasks icon-white"></i> ';
		$html[] = htmlspecialchars($name, ENT_COMPAT, 'UTF-8');
		$html[] = '</span>';
	
		return implode('', $html);
	}
	
	/**
	 * Returns the access level(s) of an item as label
	 *
	 * @param     integer    $id    The access level id
	 *
	 * @return    string            The label html
	 */
	public static function access($id = null)
	{
		static $is_admin = null;
		static $cache    = array();

		if (is_null($is_admin)) {
			$is_admin = JFactory::getUser()->authorise('core.admin');
		}

		if (!$is_admin || !$id) {
			return '';
		}

		if (!isset($cache[$id]) && $id) {
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true);

			$cache[$id] = array();

			$query->select('rules')
			->from('#__viewlevels')
			->where('id = ' . $db->quote((int) $id));

			$db->setQuery($query);
			$rules = $db->loadResult();

			if ($rules) {
				$ids = json_decode($rules);

				foreach ($ids AS $gid)
				{
					$query->clear();
					$query->select('title')
					->from('#__usergroups')
					->where('id = ' . $db->quote((int) $gid));

					$db->setQuery($query);
					$title = $db->loadResult();

					if ($title) {
						$cache[$id][] = htmlspecialchars($title, ENT_COMPAT, 'UTF-8');
					}
				}
			}
		}

		$titles = $cache[$id];
		$count  = count($titles);
		$html   = array();

		if ($count == 1) {
			$html[] = '<span class="label access">';
			$html[] = '<i class="icon-eye icon-white"></i> ';
			$html[] = htmlspecialchars($titles[0], ENT_COMPAT, 'UTF-8');
			$html[] = '</span>';
		}
		else {
			$count = $count - 1;
			$name  = array_reverse($titles);
			$name  = array_pop($name);
			$name  = trim($name);

			$tooltip = JText::_('JGRID_HEADING_ACCESS') . '::' . htmlspecialchars(implode('<br/>', $titles), ENT_COMPAT, 'UTF-8');

			$html[] = '<span class="label hasTip" title="' . $tooltip . '" style="cursor: help">';
			$html[] = '<i class="icon-eye icon-white"></i> ';
			$html[] = htmlspecialchars($name, ENT_COMPAT, 'UTF-8') . ' +' . $count;
			$html[] = '</span>';
		}

		return implode('', $html);
	}
	
	private static function hex2rgb($hex) 
	{
		$hex = str_replace("#", "", $hex);

		if(strlen($hex) == 3) {
			$r = hexdec(substr($hex,0,1).substr($hex,0,1));
			$g = hexdec(substr($hex,1,1).substr($hex,1,1));
			$b = hexdec(substr($hex,2,1).substr($hex,2,1));
		}else{
			$r = hexdec(substr($hex,0,2));
			$g = hexdec(substr($hex,2,2));
			$b = hexdec(substr($hex,4,2));
		}
		$rgb = array($r, $g, $b);

		return $rgb; // returns an array with the rgb values
	}
}