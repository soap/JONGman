<?php
defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * JONGman Component HTML Helper
 *
 * @since  1.5
 */
abstract class JHtmlIcons
{


	/**
	 * Method to generate a link to the email item page for the given article
	 *
	 * @param   object    $article  The article information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the email item link
	 */
	public static function email($article, $params, $attribs = array(), $legacy = false)
	{
		require_once JPATH_SITE . '/components/com_mailto/helpers/mailto.php';

		$uri      = JUri::getInstance();
		$base     = $uri->toString(array('scheme', 'host', 'port'));
		$template = JFactory::getApplication()->getTemplate();
		$link     = $base . JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catid, $article->language), false);
		$url      = 'index.php?option=com_mailto&tmpl=component&template=' . $template . '&link=' . MailToHelper::addLink($link);

		$status = 'width=400,height=350,menubar=yes,resizable=yes';

		if ($params->get('show_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'system/emailButton.png', JText::_('JGLOBAL_EMAIL'), null, true);
			}
			else
			{
				$text = '<span class="icon-envelope"></span>' . JText::_('JGLOBAL_EMAIL');
			}
		}
		else
		{
			$text = JText::_('JGLOBAL_EMAIL');
		}

		$attribs['title']   = JText::_('JGLOBAL_EMAIL');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
		$attribs['rel']     = 'nofollow';

		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

		return $output;
	}

	/**
	 * Display an edit icon for the article.
	 *
	 * This icon will not display in a popup window, nor if the article is trashed.
	 * Edit access checks must be performed in the calling code.
	 *
	 * @param   object    $article  The article information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string	The HTML for the article edit icon.
	 *
	 * @since   1.6
	 */
	public static function edit($article, $params, $attribs = array(), $legacy = false)
	{
		$user = JFactory::getUser();
		$uri  = JUri::getInstance();

		// Ignore if in a popup window.
		if ($params && $params->get('popup'))
		{
			return;
		}

		// Ignore if the state is negative (trashed).
		if ($article->state < 0)
		{
			return;
		}

		JHtml::_('bootstrap.tooltip');

		// Show checked_out icon if the article is checked out by a different user
		if (property_exists($article, 'checked_out')
				&& property_exists($article, 'checked_out_time')
				&& $article->checked_out > 0
				&& $article->checked_out != $user->get('id'))
		{
			$checkoutUser = JFactory::getUser($article->checked_out);
			$date         = JHtml::_('date', $article->checked_out_time);
			$tooltip      = JText::_('JLIB_HTML_CHECKED_OUT') . ' :: ' . JText::sprintf('COM_CONTENT_CHECKED_OUT_BY', $checkoutUser->name)
			. ' <br /> ' . $date;

			if ($legacy)
			{
				$button = JHtml::_('image', 'system/checked_out.png', null, null, true);
				$text   = '<span class="hasTooltip" title="' . JHtml::tooltipText($tooltip . '', 0) . '">'
						. $button . '</span> ' . JText::_('JLIB_HTML_CHECKED_OUT');
			}
			else
			{
				$text = '<span class="hasTooltip icon-lock" title="' . JHtml::tooltipText($tooltip . '', 0) . '"></span> ' . JText::_('JLIB_HTML_CHECKED_OUT');
			}

			$output = JHtml::_('link', '#', $text, $attribs);

			return $output;
		}

		$url = 'index.php?option=com_content&task=article.edit&a_id=' . $article->id . '&return=' . base64_encode($uri);

		if ($article->state == 0)
		{
			$overlib = JText::_('JUNPUBLISHED');
		}
		else
		{
			$overlib = JText::_('JPUBLISHED');
		}

		$date   = JHtml::_('date', $article->created);
		$author = $article->created_by_alias ? $article->created_by_alias : $article->author;

		$overlib .= '&lt;br /&gt;';
		$overlib .= $date;
		$overlib .= '&lt;br /&gt;';
		$overlib .= JText::sprintf('COM_CONTENT_WRITTEN_BY', htmlspecialchars($author, ENT_COMPAT, 'UTF-8'));

		if ($legacy)
		{
			$icon = $article->state ? 'edit.png' : 'edit_unpublished.png';

			if (strtotime($article->publish_up) > strtotime(JFactory::getDate())
					|| ((strtotime($article->publish_down) < strtotime(JFactory::getDate())) && $article->publish_down != JFactory::getDbo()->getNullDate()))
			{
				$icon = 'edit_unpublished.png';
			}

			$text = JHtml::_('image', 'system/' . $icon, JText::_('JGLOBAL_EDIT'), null, true);
		}
		else
		{
			$icon = $article->state ? 'edit' : 'eye-close';

			if (strtotime($article->publish_up) > strtotime(JFactory::getDate())
					|| ((strtotime($article->publish_down) < strtotime(JFactory::getDate())) && $article->publish_down != JFactory::getDbo()->getNullDate()))
			{
				$icon = 'eye-close';
			}

			$text = '<span class="hasTooltip icon-' . $icon . ' tip" title="' . JHtml::tooltipText(JText::_('COM_CONTENT_EDIT_ITEM'), $overlib, 0, 0)
			. '"></span>'
					. JText::_('JGLOBAL_EDIT');
		}

		$output = JHtml::_('link', JRoute::_($url), $text, $attribs);

		return $output;
	}

	/**
	 * Method to generate a popup link to print the reservation
	 *
	 * @param   object    $reservation  The reservation information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 */
	public static function print_popup($reservation, $params, $attribs = array(), $legacy = false)
	{
		$app = JFactory::getApplication();
		$input = $app->input;

		$url  = JRoute::_('index.php?option=com_jongman&view=reservationitem&id='.$reservation->id);
		$url .= '&tmpl=component&print=1&layout=print';

		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1024,height=768,directories=no,location=no';

		// Checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
			}
			else
			{
<<<<<<< HEAD
				$text = '<i class="icon-print glyphicon glyphicon-print"></i>'.JText::_('JGLOBAL_PRINT');
=======
				$text = '<span class="icon-print"></span>' . JText::_('JGLOBAL_PRINT');
>>>>>>> f260c473c4627674d709964076fdcb5b4545f5fb
			}
		}
		else
		{
			$text = JText::_('JGLOBAL_PRINT');
		}

		$attribs['title']   = JText::_('JGLOBAL_PRINT');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
		$attribs['rel']     = 'nofollow';
<<<<<<< HEAD
		$attribs['class'] = 'btn btn-info hasTooltip';
		
		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}

	/**
	 * Method to generate a popup link to reservation in pdf format
	 *
	 * @param   object    $reservation  The reservation information
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Optional attributes for the link
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 */
	public static function pdf_popup($reservation, $params, $attribs = array(), $legacy = false)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
	
		$url  = JRoute::_('index.php?option=com_jongman&view=reservationitem&id='.$reservation->id);
		$url .= '&tmpl=component&print=1&layout=print&format=pdf';
	
		$status = 'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=1024,height=768,directories=no,location=no';
	
		// Checks template image directory for image, if non found default are loaded
		if ($params->get('show_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'system/pdfButton.png', JText::_('COM_JONGMAN_GLOBAL_PDF'), null, true);
			}
			else
			{
				$text = '<i class="icon-file glyphicon glyphicon-file"></i>'.JText::_('COM_JONGMAN_GLOBAL_PDF');
			}
		}
		else
		{
			$text = JText::_('COM_JONGMAN_GLOBAL_PDF');
		}
	
		$attribs['title']   = JText::_('COM_JONGMAN_GLOBAL_PDF');
		$attribs['onclick'] = "window.open(this.href,'win2','" . $status . "'); return false;";
		$attribs['rel']     = 'nofollow';
		$attribs['class'] = 'btn btn-info hasTooltip';
	
		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}
=======

		return JHtml::_('link', JRoute::_($url), $text, $attribs);
	}

>>>>>>> f260c473c4627674d709964076fdcb5b4545f5fb
	/**
	 * Method to generate a link to print an article
	 *
	 * @param   Registry  $params   The item parameters
	 * @param   array     $attribs  Not used, @deprecated for 4.0
	 * @param   boolean   $legacy   True to use legacy images, false to use icomoon based graphic
	 *
	 * @return  string  The HTML markup for the popup link
	 */
	public static function print_screen($params, $attribs = array(), $legacy = false)
	{
		// Checks template image directory for image, if none found default are loaded
		if ($params->get('show_icons'))
		{
			if ($legacy)
			{
				$text = JHtml::_('image', 'system/printButton.png', JText::_('JGLOBAL_PRINT'), null, true);
			}
			else
			{
				$text = '<span class="icon-print"></span>' . JText::_('JGLOBAL_PRINT');
			}
		}
		else
		{
			$text = JText::_('JGLOBAL_PRINT');
		}

		return '<a href="#" onclick="window.print();return false;">' . $text . '</a>';
	}
}