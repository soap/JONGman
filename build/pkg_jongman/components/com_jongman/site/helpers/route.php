<?php
/**
 * @package      Projectfork
 * @subpackage   Projects
 *
 * @author       Tobias Kuhn (eaxs)
 * @copyright    Copyright (C) 2006-2012 Tobias Kuhn. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.txt
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
