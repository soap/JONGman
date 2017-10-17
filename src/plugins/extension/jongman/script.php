<?php
/**
  JONGman - Resource Reservation extension for Joomla
  ------------------------------------------------------------------------
  @Author    JONGman Team
  @Website   http://www.joomlant.com
  @Copyright Copyright (C) 2013 - 2015 JONGman Project. All Rights Reserved.
  @License   GNU General Public License version 3, or later
------------------------------------------------------------------------*/

defined('_JEXEC') or die;

/**
 * Custom script to hook into installation process
 *
 */
class plgExtensionJongmanInstallerScript {

	function install($parent) {
		//echo '<p>'. JText::_('3.0 Custom install script') . '</p>';
	}

	function uninstall($parent) {
		//echo '<p>'. JText::_('3.0 Custom uninstall script') .'</p>';
	}

	function update($parent) {
		//echo '<p>'. JText::_('3.0 Custom update script') .'</p>';
	}

	function preflight($type, $parent) {
		//echo '<p>'. JText::sprintf('3.0 Preflight for %s', $type) .'</p>';
	}

	function postflight($type, $parent) {
		echo '<p>'. JText::_('PLG_EXTENSION_JONGMAN_PLUGIN_INSTALL_SUCCESS') .'</p>';
			
		$dbo = JFactory::getDbo();
		
		$query = $dbo->getQuery(true);
		
		$query->clear();
		$query->update($dbo->quoteName('#__extensions'));
		$query->set('enabled = 1');
		$query->where("element = 'jongman'");
		$query->where("type = 'plugin'");
		$query->where("folder = 'extension'");

		$dbo->setQuery($query);
		
		$result = $dbo->execute();
		if(!$result) {
			JError::raiseWarning(-1, 'plgExtensionJongman: publishing failed');
		} else {
			echo '<p>'. JText::_('PLG_EXTENSION_JONGMAN_PLUGIN_PUBLISH_SUCCESS') .'</p>';
		}
	}
}