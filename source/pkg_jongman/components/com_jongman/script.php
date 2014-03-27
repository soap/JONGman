<?php
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
		// Note: this file is executed in the tmp folder if using the upload method.
		if ($type == 'install') {
			$parent->getParent()->setRedirectURL('index.php?option=com_jongman&view=postinstall');
		}
		return true;
	}
}
