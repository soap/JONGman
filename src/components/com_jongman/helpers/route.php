<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die();


jimport('joomla.application.component.helper');


/**
 * Projects Component Route Helper
 *
 * @static
 */
abstract class JongmanHelperRoute
{
    protected static $lookup;

    /**
     * Creates a link to the projects overview
     *
     * @return    string    $link    The link
     */
    public static function getReservationsRoute()
    {
        $link = 'index.php?option=com_jongman&view=reservations';

        if ($item = RFApplicationHelper::itemRoute(null, 'com_jongman.reservations')) {
            $link .= '&Itemid=' . $item;
        }

        return $link;
    }
}
