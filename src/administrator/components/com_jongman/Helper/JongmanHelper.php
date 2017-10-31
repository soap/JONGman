<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Soap\Component\Jongman\Administrator\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ContentHelper;


/**
 * JONGman component helper.
 *
 * @since  1.6
 */
class JongmanHelper extends ContentHelper
{
    /**
     * Configure the JONGMan bars.
     *
     * @param   string $vName The name of the active view.
     *
     * @return  void
     *`
     * @since   1.6
     */
    public static function addSubmenu($vName)
    {
        \JHtmlSidebar::addEntry(
            \JText::_('COM_JONGMAN_SUBMENU_SCHEDULES'),
            'index.php?option=com_jongman&view=schedules',
            $vName == 'schedules'
        );

        \JHtmlSidebar::addEntry(
            \JText::_('COM_JONGMAN_SUBMENU_RESOURCES'),
            'index.php?option=com_jongman&view=resources',
            $vName == 'resources'
        );

        \JHtmlSidebar::addEntry(
            \JText::_('COM_JONGMAN_SUBMENU_LAYOUTS'),
            'index.php?option=com_jongman&view=layouts',
            $vName == 'layouts'
        );
    }
}