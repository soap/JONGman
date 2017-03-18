<?php
/**
 * Created by PhpStorm.
 * User: soap
 * Date: 16/03/2017
 * Time: 23:33
 */

/**
 * @package     Joomla.Administrator
 * @subpackage  com_weblinks
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Acceptance cest object class for admin steps
 *
 * @package  Administrator
 *
 * @since    1.0
 */
class AdministratorJongmanCest
{
    /**
     * User constructor.
     *
     * @since   version
     */
    public function __construct()
    {
        $this->faker = Faker\Factory::create();
        $this->title  = 'Jongman' . $this->faker->randomNumber();
        $this->url  = $this->faker->url();
    }

    /**
     * Method to verify available tabs
     *
     * @param   string  $I  The schedule object
     *
     * @since   version
     *
     * @return  void
     */
    public function administratorVerifyAvailableTabs(\Step\Acceptance\schedule $I)
    {
        $I->am('Administrator');
        $I->wantToTest('Weblinks Edit View Tabs');

        $I->doAdministratorLogin();

        $I->amGoingTo('Navigate to Weblinks page in /administrator/ and verify the Tabs');
        $I->amOnPage('administrator/index.php?option=com_weblinks&view=weblinks');
        $I->waitForText('Web Links', '30', ['css' => 'h1']);
        $I->clickToolbarButton('New');
        $I->waitForText('Web Link: New', '30', ['css' => 'h1']);
        $I->verifyAvailableTabs(['New Web Link', 'Images', 'Publishing', 'Options', 'Metadata']);
    }

    /**
     * Method to create schedule
     *
     * @param   string  $I  The schedule object
     *
     * @since   version
     *
     * @return  void
     */
    public function administratorCreateSchedule(\Step\Acceptance\schedule $I)
    {
        $I->am('Administrator');
        $I->wantToTest('Weblink creation in /administrator/');

        $I->doAdministratorLogin();

        // Get the weblink StepObject
        $I->amGoingTo('Navigate to Weblinks page in /administrator/');
        $I->amOnPage('administrator/index.php?option=com_weblinks');
        $I->waitForText('Web Links', '30', ['css' => 'h1']);
        $I->expectTo('see weblinks page');
        $I->checkForPhpNoticesOrWarnings();

        $I->amGoingTo('try to save a weblink with a filled title and URL');
        $I->clickToolbarButton('New');
        $I->waitForText('Web Link: New', '30', ['css' => 'h1']);
        $I->fillField(['id' => 'jform_title'], $this->title);
        $I->fillField(['id' => 'jform_url'], $this->url);
        $I->clickToolbarButton('Save & Close');
        $I->waitForText('Web Links', '30', ['css' => 'h1']);
        $I->expectTo('see a success message and the weblink added after saving the weblink');
        $I->see('Web link successfully saved', ['id' => 'system-message-container']);
        $I->see($this->title, ['id' => 'weblinkList']);
    }

    /**
     * Method to trash weblink
     *
     * @param   string  $I  The weblink object
     *
     * @since   version
     *
     * @depends administratorCreateWeblink
     *
     * @return  void
     */
    public function administratorTrashWeblink(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('Weblink removal in /administrator/');

        $I->doAdministratorLogin();

        $I->amGoingTo('Navigate to Weblinks page in /administrator/');
        $I->amOnPage('administrator/index.php?option=com_weblinks');
        $I->waitForText('Web Links', '30', ['css' => 'h1']);
        $I->expectTo('see weblinks page');

        $I->amGoingTo('Search the just saved weblink');
        $I->searchForItem($this->title);
        $I->waitForText('Web Links', '30', ['css' => 'h1']);

        $I->amGoingTo('Delete the just saved weblink');
        $I->checkAllResults();
        $I->clickToolbarButton('Trash');
        $I->waitForText('Web Links', '30', ['css' => 'h1']);
        $I->expectTo('see a success message and the weblink removed from the list');
        $I->see('Web link successfully trashed', ['id' => 'system-message-container']);
        $I->cantSee($this->title, ['id' => 'weblinkList']);
    }

    /**
     * Method to delete schedule
     *
     * @param   string  $I  The jongman object
     *
     * @since   version
     *
     * @depends administratorCreateWeblink
     *
     * @return  void
     */
    public function administratorDeleteWeblink(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('Weblink removal in /administrator/');

        $I->doAdministratorLogin();

        $I->amGoingTo('Navigate to Weblinks page in /administrator/');
        $I->amOnPage('administrator/index.php?option=com_weblinks');
        $I->waitForText('Web Links', '30', ['css' => 'h1']);
        $I->expectTo('see weblinks page');
        $I->click('Search Tools');
        $I->wait(2);
        $I->selectOptionInChosenById('filter_published', 'Trashed');
        $I->amGoingTo('Search the just saved weblink');
        $I->searchForItem($this->title);
        $I->waitForText('Web Links', '30', ['css' => 'h1']);

        $I->amGoingTo('Delete the just saved weblink');
        $I->checkAllResults();
        $I->click(['xpath' => '//div[@id="toolbar-delete"]/button']);
        $I->acceptPopup();
        $I->waitForText('Web Links', '30', ['css' => 'h1']);
        $I->expectTo('see a success message and the weblink removed from the list');
        $I->see('1 web link successfully deleted.', ['id' => 'system-message-container']);
        $I->cantSee($this->title, ['id' => 'weblinkList']);
    }

    /**
     * Method to test schedule creation
     *
     * @param   string  $I  The Jongamn object
     *
     * @since   version
     *
     * @depends administratorCreateJongman
     *
     * @return  void
     */
    public function administratorCreateScheduleWithoutTitleFails(AcceptanceTester $I)
    {
        $I->am('Administrator');
        $I->wantToTest('Jongman creation without title fails in /administrator/');

        $I->doAdministratorLogin();
        $I->amGoingTo('Navigate to Jongman page in /administrator/');
        $I->amOnPage('administrator/index.php?option=com_jongman&view=schedule');
        $I->waitForText('JONGman', '30', ['css' => 'h1']);
        $I->expectTo('see JONGman page');
        $I->checkForPhpNoticesOrWarnings();

        $I->amGoingTo('try to save a schedule with empty title and it should fail');
        $I->click(['xpath' => "//div[@id='toolbar-new']//button"]);
        $I->waitForText('Schedule: New', '30', ['css' => 'h1']);
        $I->click(['xpath' => "//div[@id='toolbar-apply']//button"]);
        $I->expectTo('see an error when trying to save a schedule without title and without URL');
        $I->see('Invalid field:  Title', ['id' => 'system-message-container']);
        $I->see('Invalid field:  URL', ['id' => 'system-message-container']);
    }
}