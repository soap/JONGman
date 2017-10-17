<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

/**
 * @package     Cash Payment System
 * @subpackage  com_cashpayment
 * @since       2.0
 */
class com_jongmanInstallerScript
{
	/**
	 * Runs after files are installed and database scripts executed.
	 *
	 * @param   JInstaller  $parent  The installer object.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	function install($parent)
	{
		// Add custom code.
		return true;
	}

	/**
	 * Runs after files are removed and database scripts executed.
	 *
	 * @param   JInstaller  $parent  The installer object.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	function uninstall($parent)
	{
		// Add custom code.
		return true;
	}

	/**
	 * Runs after files are updated and database scripts executed.
	 *
	 * @param   JInstaller  $parent  The installer object.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	function update($parent)
	{
		// Add custom code.

		return true;
	}

	/**
	 * Runs before anything is run.
	 *
	 * @param   string      $type    The type of installation: install|update.
	 * @param   JInstaller  $parent  The installer object.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	function preflight($type, $parent)
	{
		// Add custom code.
		return true;
	}

	/**
	 * Runs after an extension install or update.
	 *
	 * @param   string      $type    The type of installation: install|update.
	 * @param   JInstaller  $parent  The installer object.
	 *
	 * @return  void
	 *
	 * @since   2.0
	 */
	function postflight($type, $parent)
	{
		// Add custom code.
		$errFile	= array();
		$sccFile	= array();
		$msgError	= '';
		$msgSuccess	= '';
		
		//self::installPdfView($sccFile, $errFile);
		
		if (!empty($sccFile)) {
			$msgSuccess .= '<br />' . implode("<br />", $sccFile);
		}
		
		if (!empty($errFile)) {
			$msgError .= '<br />' . implode("<br />", $errFile);
		}
		
		// END MESSAGE
		if ($msgError != '') {
			$msg = '<span style="font-weight: bold;color:#ff0000;">'.JText::_('COM_JONGMAN_ERROR_INSTALL').'</span>: ' . $msgSuccess . $msgError;
			JFactory::getApplication()->enqueueMessage($msg, 'error');
		} else {
			$msg = '<span style="font-weight: bold;color:#00cc00;">'.JText::_('COM_JONGMAN_SUCCESS_INSTALL').'</span>: ' . $msgSuccess;
			
			JFactory::getApplication()->enqueueMessage($msg, 'message');
		}
		
		
		// Note: this file is executed in the tmp folder if using the upload method.
		if ($type == 'install') {
			$msg .= JText::_('COMJONGMAN_INSTALLATION_NOT_COMPLETE');
			$parent->getParent()->setRedirectURL('index.php?option=com_jongman&view=postinstall');
	
		}		
	}
	
	function installPdfView(&$sccFile, &$errFile) 
	{	
		$success 	= '<span style="font-weight: bold;color:#00cc00;">'.JText::_('COM_JONGMAN_SUCCESS').'</span> - ';
		$error 		= '<span style="font-weight: bold;color:#ff0000;">'.JText::_('COM_JONGMAN_ERROR').'</span> - ';
		jimport( 'joomla.client.helper' );
		jimport( 'joomla.filesystem.file' );
		jimport( 'joomla.filesystem.folder' );
		$ftp 	= JClientHelper::setCredentialsFromRequest('ftp');
		
		$src 	= JPATH_ROOT.'/administrator/components/com_jongman/install/files/pdf';
		$dest 	= JPATH_ROOT.'/libraries/joomla/document/pdf';
		
		if (JFolder::exists($src)) {
			if (!JFolder::copy($src, $dest, null, true)) {
				$errFile[]	= $error . JText::_( 'COM_JONGMAN_FOLDER_COPYING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_JONGMAN_SOURCE_FOLDER' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src)
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_JONGMAN_DESTINATION_FOLDER' ). ': ' . str_replace( JPATH_ROOT . '/', '', $dest);
			}else {
				$sccFile[]	= $success . JText::_( 'COM_JONGMAN_FOLDER_COPYING' ). ': '
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_JONGMAN_SOURCE_FOLDER' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src)
					. '<br />&nbsp;&nbsp; - ' . JText::_( 'COM_JONGMAN_DESTINATION_FOLDER' ). ': ' . str_replace( JPATH_ROOT . '/', '', $dest);
			}
		}else{
			$errFile[] = $error . JText::_( 'COM_JONGMAN_ERROR_FOLDER_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $src);
		}	
		
		if (!file_exists($dest)) {
			$errFile[] = $error . JText::_( 'COM_JONGMAN_ERROR_FOLDER_NOT_EXIST' ). ': ' . str_replace( JPATH_ROOT . '/', '', $dest);
		}
		
		return true; // will not worked, we are working with errorMsg
	}
	
}
