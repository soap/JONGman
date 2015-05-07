<?php
/**
 * @package      pkg_jongman
 * @subpackage   lib_jongman
 *
 * @author       Prasit Gebsaap
 * @copyright    Copyright (C) 2007-2013 Prasit Gebsaap. All rights reserved.
 * @license      http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.txt
 */

defined('_JEXEC') or die();


/**
 * Version information class for the JONGman package.
 *
 */
final class JMVersion
{
    /** @var  string  Product name. */
    public $PRODUCT = 'JONGman Library';

    /** @var  string  Release version. */
    public $RELEASE = '3.0';

    /** @var  string  Maintenance version. */
    public $DEV_LEVEL = '1';

    /** @var  string  Development status. */
    public $DEV_STATUS = 'releae candidate';

    /** @var  string  Build number. */
    public $BUILD = '9';

    /** @var  string  Code name. */
    public $CODENAME = 'Nada';

    /** @var  string  Release date. */
    public $RELDATE = '30-June-2013';

    /** @var  string  Release time. */
    public $RELTIME = '20:00';

    /** @var  string  Release timezone. */
    public $RELTZ = 'CET';

    /** @var  string  Copyright Notice. */
    public $COPYRIGHT = 'Copyright (C) 2007 - 2014 Prasit Gebsaap. All rights reserved.';

    /** @var  string  Link text. */
    public $URL = '<a href="http://www.joomlant.org">joomlant.org</a> is Free Software released under the GNU General Public License.';


    /**
     * Compares two a "PHP standardized" version number against the current JONGman version.
     *
     * @param     string    $minimum    The minimum version of JONGman which is compatible.
     *
     * @return    bool                  True if the version is compatible.
     */
    public function isCompatible($minimum)
    {
        return version_compare(JMVERSION, $minimum, 'ge');
    }


    /**
     * Gets a "PHP standardized" version string for the current Projectfork.
     *
     * @return    string    Version string.
     */
    public function getShortVersion()
    {
        return $this->RELEASE . '.' . $this->DEV_LEVEL;
    }


    /**
     * Gets a version string for the current Projectfork with all release information.
     *
     * @return    string    Complete version string.
     */
    public function getLongVersion()
    {
        return $this->PRODUCT . ' ' . $this->RELEASE . '.' . $this->DEV_LEVEL . ' '
                . $this->DEV_STATUS . ' [ ' . $this->CODENAME . ' ] ' . $this->RELDATE . ' '
                . $this->RELTIME . ' ' . $this->RELTZ;
    }
}
