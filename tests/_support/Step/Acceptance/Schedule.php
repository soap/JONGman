<?php

namespace Step\Acceptance;

/**
 * Class Schedule
 *
 * Step Object to interact with a schedule
 *
 * @todo: this class should grow until being able to execute generic operations over a Schedule: change status, ..
 *
 * @package Step\Acceptance
 * @see http://codeception.com/docs/06-ReusingTestCode#StepObjects
 */
class Schedule extends \AcceptanceTester
{
    /**
     * Creates a weblink
     *
     * @param   string  $title          The title for the weblink
     * @param   string  $url            The url for the weblink
     * @param   string  $countClicks    If not null, we set the "Count Clicks" weblink property to the given value.
     *
     */
    public function createSchedule($title, $url, $countClicks = null)
    {
        $I = $this;

        $I->comment('I navigate to JONGman page in /administrator/');
        $I->amOnPage('administrator/index.php?option=com_jongman&view=schedule');
        $I->waitForText('JONGman', '30', ['css' => 'h1']);
        $I->comment('I see JONGman page');

        $I->comment('I try to save a schedule with a filled title');
        $I->click('New');
        $I->waitForText('Schedule: New', '30', ['css' => 'h1']);
        $I->fillField(['id' => 'jform_title'], $title);
        $I->fillField(['id' => 'jform_url'], $url);

        if ($countClicks !== null) {
            $I->click(['link' => 'Options']);
            $I->selectOptionInChosen("Count Clicks", $countClicks);
        }

        $I->clickToolbarButton('Save & Close');
        $I->waitForText('Schedule successfully saved', '30', ['id' => 'system-message-container']);
    }

    public function administratorDeleteWeblink($title)
    {
        $I = $this;

        $I->amGoingTo('Navigate to Weblinks page in /administrator/');
        $I->amOnPage('administrator/index.php?option=com_weblinks');
        $I->waitForText('Web Links','30',['css' => 'h1']);
        $I->expectTo('see weblinks page');

        $I->amGoingTo('Search for the weblink');
        $I->searchForItem($title);
        $I->waitForText('Web Links','30',['css' => 'h1']);

        $I->amGoingTo('Trash the weblink');
        $I->checkAllResults();
        $I->clickToolbarButton('Trash');
        $I->waitForText('Web Links','30',['css' => 'h1']);
        $I->waitForText('1 web link successfully trashed', 30, ['id' => 'system-message-container']);

        $I->amGoingTo('Delete the weblink');
        $I->selectOptionInChosen('- Select Status -', 'Trashed');
        $I->amGoingTo('Search the just saved weblink');
        $I->searchForItem($title);
        $I->waitForText('Web Links','30',['css' => 'h1']);
        $I->checkAllResults();
        $I->click(['xpath'=> '//div[@id="toolbar-delete"]/button']);
        $I->acceptPopup();
        $I->waitForText('Web Links','30',['css' => 'h1']);
        $I->waitForText('1 web link successfully deleted.', 30, ['id' => 'system-message-container']);
    }
}