<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

class JongmanRouter extends JComponentRouterView
{
    protected $noIDs = false;

    /**
     * Content Component router constructor
     *
     * @param   JApplicationCms  $app   The application object
     * @param   JMenu            $menu  The menu object to work with
     */
    public function __construct($app = null, $menu = null)
    {
        $params = JComponentHelper::getParams('com_jongman');
        $this->noIDs = (bool) $params->get('sef_ids');

        // this view as attached key with menu item
        $schedule = new JComponentRouterViewconfiguration('schedule');
        $schedule->addLayout('calendar')->setKey('id');
        $this->registerView($schedule);

        $reservation = new JComponentRouterViewconfiguration('reservation');
        $reservation->setKey('reference_number');
        $this->registerView($reservation);

        $calendar = new JComponentRouterViewconfiguration('calendar');
        $this->registerView($calendar);

        $reservations = new JComponentRouterViewconfiguration('reservations');
        $this->registerView($reservations);

        parent::__construct($app, $menu);

        $this->attachRule(new JComponentRouterRulesMenu($this));

        if ($params->get('sef_advanced', 0))
        {
            $this->attachRule(new JComponentRouterRulesStandard($this));
            $this->attachRule(new JComponentRouterRulesNomenu($this));
        }
        else {
            JLoader::register('JongmanRouterRulesLegacy', __DIR__ . '/helpers/legacyrouter.php');
            $this->attachRule(new JongmanRouterRulesLegacy($this));
        }
    }

    /**
     * Method to get the segment for a schedule
     *
     * @param   string  $id     ID of the schedule to retrieve the segment for
     * @param   array   $query  The request that is built right now
     * @since   3.8.0
     * @return  string  The segment of this item
     */
    public function getScheduleSegment($id, $query)
    {
        if (!strpos($id, ':'))
        {
            $db = JFactory::getDbo();
            $dbquery = $db->getQuery(true);
            $dbquery->select($dbquery->qn('alias'))
                ->from($dbquery->qn('#__jongman_schedules'))
                ->where('id = ' . $dbquery->q((int) $id));
            $db->setQuery($dbquery);
            $id .= ':' . $db->loadResult();
        }

        if ($this->noIDs) {
            list($void, $segment) = explode(':', $id, 2);
            $result = array($void => $segment);
            if (isset($query['sd'])) {
                $result['sd'] = $query['sd'];
                unset($query['sd']);
            }
            return $result;
        }

        if (isset($query['sd'])) {
            $result['sd'] = $query['sd'];
            unset($query['sd']);
        }
        return array((int) $id => $id);
    }

    /**
     * Method to get the id for a schedule
     *
     * @param   string  $segment  Segment to retrieve the ID for
     * @param   array   $query    The request that is parsed right now
     *
     * @return  mixed   The id of this item or false
     */
    public function getSchedueId($segment, $query)
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('id')->from('#__jongman_schedules')->where(['alias'=>$segment]);
        $db->setQuery($query);
        $row = $db->loadObject();
        if ($row) {
            return $row->id;
        }

        return false;

    }
}

/**
 * Parse the segments of a URL.
 *
 * This function is a proxy for the new router interface
 * for old SEF extensions.
 *
 * @param   array  $segments  The segments of the URL to parse.
 *
 * @return  array  The URL attributes to be used by the application.
 *
 * @since   3.1
 * @deprecated  4.0  Use Class based routers instead
 */
function JongmanBuildRoute( &$query )
{
    $app = JFactory::getApplication();
    $router = new JongmanRouter($app, $app->getMenu());

    return $router->build($query);
}

function JongmanParseRoute( &$segments )
{
    $app = JFactory::getApplication();
    $router = new JongmanRouter($app, $app->getMenu());

    return $router->parse($segments);
}

