<?php
/**
 * Created by PhpStorm.
 * User: soap
 * Date: 16/03/2017
 * Time: 23:26
 */

/**
 * @package     Joomla.Administrator
 * @subpackage  com_jongman
 *
 * @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

class InstallJongmanCest
{
    public function installJoomla(\AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->installJoomlaRemovingInstallationFolder();
        $I->doAdministratorLogin();
        $I->disableStatistics();
        $I->setErrorReportingToDevelopment();
    }

    /**
     * @depends installJoomla
     */
    public function installJongman(\AcceptanceTester $I)
    {
        $I->doAdministratorLogin();
        $I->comment('get JONGman repository folder from acceptance.suite.yml (see _support/AcceptanceHelper.php)');

        // URL where the package file to install is located (mostly the same as joomla-cms)
        $url = $I->getConfiguration('url');
        $I->installExtensionFromUrl($url . "/pkg-jongman-current.zip");
        $I->doAdministratorLogout();
    }

}