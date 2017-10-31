<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Soap\Component\Jongman\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Table\Table;

/**
 * Methods supporting a list of banner records.
 *
 * @since  1.6
 */
class LayoutsModel extends ListModel
{
    /**
     * Constructor override.
     *
     * @param   array  $config  An optional associative array of configuration settings.
     *
     * @return  LayoutsModel
     * @since   1.0
     * @see     JModelList
     */

    public function __construct($config = array())
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = array(
                'id', 'a.id',
                'title', 'a.title',
                'alias', 'a.alias', 'a.default', 'a.timezone',
                'checked_out', 'a.checked_out',
                'checked_out_time', 'a.checked_out_time',
                'published', 'a.published',
                'access', 'a.access', 'access_level',
                'ordering', 'a.ordering',
                'timzone', 'a.timezone',
                'created', 'a.created',
                'created_by', 'a.created_by',
                'modified', 'a.modified',
                'modified_by', 'a.modified_by',
            );
        }
        parent::__construct($config);
    }

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string  $type    The table type to instantiate
     * @param   string  $prefix  A prefix for the table class name. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  Table  A Table object
     *
     * @since   1.6
     */
    public function getTable($type = 'Layout', $prefix = 'Administrator', $config = array())
    {
        return parent::getTable($type, $prefix, $config);
    }

    /**
     * Method to auto-populate the model state.
     *
     * Note. Calling getState in this method will result in recursion.
     *
     * @param   string  $ordering   An optional ordering field.
     * @param   string  $direction  An optional direction (asc|desc).
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function populateState($ordering = 'a.title', $direction = 'asc')
    {
        // Load the parameters.
        $this->setState('params', ComponentHelper::getParams('com_jongman'));

        // List state information.
        parent::populateState($ordering, $direction);
    }

    /**
     * Build an SQL query to load the list data.
     *
     * @return  \JDatabaseQuery
     *
     * @since   1.6
     */
    protected function getListQuery()
    {
        $db = $this->getDbo();
        $query = $db->getQuery(true);
        // Select the required fields from the table.
        $query->select(
            $this->getState(
                'list.select',
                'a.id,'
                .'a.title,'
                .'a.alias,'
                .'a.timezone,'
                .'a.default,'
                .'a.checked_out,'
                .'a.checked_out_time,'
                .'a.ordering,'
                .'a.published,'
                .'a.access,'
                .'a.created,'
                .'a.language,'
                .'(SELECT COUNT(s.id) FROM #__jongman_schedules AS s WHERE s.layout_id=a.id) as used_count'
            )
        );

        $query->from('#__jongman_layouts AS a');

        // Join over the language
        $query->select('l.title AS language_title');
        $query->join('LEFT', '`#__languages` AS l ON l.lang_code = a.language');

        // Join over the users for the checked out user.
        $query->select('uc.name AS editor');
        $query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

        // Join over the asset groups.
        $query->select('ag.title AS access_level');
        $query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');

        // Join over the users for the author.
        $query->select('ua.name AS author_name');
        $query->join('LEFT', '#__users AS ua ON ua.id = a.created_by');

        // Filter by search in title
        $search = $this->getState('filter.search');

        if (!empty($search)) {
            if (stripos($search, 'id:') === 0) {
                $query->where('a.id = '.(int) substr($search, 3));
            } else {
                $search = $db->quote('%'.$db->escape($search, true).'%');
                $query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
            }
        }

        // Filter by access level.
        if ($access = $this->getState('filter.access')) {
            $query->where('a.access = ' . (int) $access);
        }

        // Filter by published state
        $published = $this->getState('filter.published');
        if (is_numeric($published)) {
            $query->where('a.published = ' . (int) $published);
        } else if ($published === '') {
            $query->where('(a.published = 0 OR a.published = 1)');
        }

        // Filter by a single or group of categories.
        $timezone = $this->getState('filter.timezone');
        if ($timezone) {
            $query->where('a.timezone = '.$db->escape($timezone));
        }

        // Add the list ordering clause.
        $orderCol	= $this->state->get('list.ordering', 'a.title');
        $orderDirn	= $this->state->get('list.direction', 'asc');

        if (!empty($orderCol)) {
            $query->order($db->escape($orderCol.' '.$orderDirn));
        }

        return $query;
    }

}
