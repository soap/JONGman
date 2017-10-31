<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Soap\Component\Jongman\Administrator\Table;

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

/**
 * Contact Table class.
 *
 * @since  1.0
 */
class ScheduleTable extends Table
{
    /**
     * Ensure the params and metadata in json encoded in the bind method
     *
     * @var    array
     * @since  3.3
     */
    protected $_jsonEncode = array('params', 'metadata');

    /**
     * Constructor
     *
     * @param   \JDatabaseDriver $db Database connector object
     * @since   1.0
     */
    public function __construct(\JDatabaseDriver $db)
    {
        $this->typeAlias = 'com_jongman.schedule';

        parent::__construct('#__jongman_schedules', 'id', $db);
    }

    /**
     * Generate a valid alias from title / date.
     * Remains public to be able to check for duplicated alias before saving
     * @since 2.0
     * @return  string
     */
    public function generateAlias()
    {
        if (empty($this->alias))
        {
            $this->alias = $this->name;
        }

        $this->alias = ApplicationHelper::stringURLSafe($this->alias, $this->language);

        if (trim(str_replace('-', '', $this->alias)) == '')
        {
            $this->alias = \JFactory::getDate()->format('Y-m-d-H-i-s');
        }

        return $this->alias;
    }
}