<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Soap\Component\Jongman\Administrator\View\Layouts;

defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Soap\Component\Jongman\Administrator\Helper\JongmanHelper;
use Joomla\CMS\Helper\ContentHelper;
/**
 * View class for a list of layouts.
 *
 * @since  1.6
 */

class HtmlView extends BaseHtmlView
{
    /**
     * An array of items
     *
     * @var  array
     */
    protected $items;

    /**
     * The pagination object
     *
     * @var  \JPagination
     */
    protected $pagination;

    /**
     * The model state
     *
     * @var  object
     */
    protected $state;


    /**
     * Method to display the view.
     *
     * @param   string $tpl A template file to load. [optional]
     *
     * @return  mixed  A string if successful, otherwise a \JError object.
     *
     * @since   1.6
     */
    public function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        if ($tpl !== 'modal') {
            JongmanHelper::addSubmenu('layouts');
            $this->sidebar = \JHtmlSidebar::render();
        }
        $this->addToolbar();

        return parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return  void
     *
     * @since   1.6
     */
    protected function addToolbar()
    {
        $user  = \JFactory::getUser();

        \JToolbarHelper::title(\JText::_('COM_JONGMAN_MANAGER_LAYOUTS'), 'bookmark layouts');

        $canDo = ContentHelper::getActions('com_jongman', 'layout');
        if ($canDo->get('core.create'))
        {
            \JToolbarHelper::addNew('layout.add');
        }

        if ($canDo->get('core.edit'))
        {
            \JToolbarHelper::editList('layout.edit');
        }

        if ($canDo->get('core.edit.state'))
        {
            if ($this->state->get('filter.published') != 2)
            {
                \JToolbarHelper::publish('layouts.publish', 'JTOOLBAR_PUBLISH', true);
                \JToolbarHelper::unpublish('layouts.unpublish', 'JTOOLBAR_UNPUBLISH', true);
            }

            if ($this->state->get('filter.published') != -1)
            {
                if ($this->state->get('filter.published') != 2)
                {
                    \JToolbarHelper::archiveList('layouts.archive');
                }
                elseif ($this->state->get('filter.published') == 2)
                {
                    \JToolbarHelper::unarchiveList('layouts.publish');
                }
            }
        }

        if ($canDo->get('core.edit.state'))
        {
            \JToolbarHelper::checkin('layouts.checkin');
        }

        // Add a batch button
        if ($user->authorise('core.create', 'com_jongman')
            && $user->authorise('core.edit', 'com_jongman')
            && $user->authorise('core.edit.state', 'com_jongman'))
        {
            $title = \JText::_('JTOOLBAR_BATCH');

            // Instantiate a new \JLayoutFile instance and render the batch button
            $layout = new \JLayoutFile('joomla.toolbar.batch');

            $dhtml = $layout->render(array('title' => $title));
            \JToolbar::getInstance('toolbar')->appendButton('Custom', $dhtml, 'batch');
        }

        if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete'))
        {
            \JToolbarHelper::deleteList('JGLOBAL_CONFIRM_DELETE', 'layouts.delete', 'JTOOLBAR_EMPTY_TRASH');
        }
        elseif ($canDo->get('core.edit.state'))
        {
            \JToolbarHelper::trash('layouts.trash');
        }

        if ($user->authorise('core.admin', 'com_jongman') || $user->authorise('core.options', 'com_jongman'))
        {
            \JToolbarHelper::preferences('com_jongman');
        }

        \JToolbarHelper::help('JHELP_COMPONENTS_JONGMAN_SCHEDULES');
    }

    /**
     * Returns an array of fields the table can be sorted by
     *
     * @return  array  Array containing the field name to sort by as the key and display text as value
     *
     * @since   3.0
     */
    protected function getSortFields()
    {
        return array(
            'ordering'    => \JText::_('JGRID_HEADING_ORDERING'),
            'a.state'     => \JText::_('JSTATUS'),
            'a.name'      => \JText::_('COM_JONGMAN_HEADING_NAME'),
            'a.language'  => \JText::_('JGRID_HEADING_LANGUAGE'),
            'a.id'        => \JText::_('JGRID_HEADING_ID'),
        );
    }
}