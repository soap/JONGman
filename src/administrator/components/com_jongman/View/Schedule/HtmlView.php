<?php
/**
 * @package     ${NAMESPACE}
 * @subpackage
 *
 * @copyright   A copyright
 * @license     A "Slug" license name e.g. GPL2
 */

namespace Soap\Component\Jongman\Administrator\View\Schedule;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

defined('_JEXEC') or die;


/**
 * View to edit a schedule.
 *
 * @since  1.5
 */
class HtmlView extends BaseHtmlView
{
    /**
     * The \JForm object
     *
     * @var  \JForm
     */
    protected $form;

    /**
     * The active item
     *
     * @var  object
     */
    protected $item;

    /**
     * The model state
     *
     * @var  object
     */
    protected $state;

    /**
     * Display the view
     *
     * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
     *
     * @return  mixed  A string if successful, otherwise an Error object.
     */
    public function display($tpl = null)
    {
        // Initialiase variables.
        $this->form  = $this->get('Form');
        $this->item  = $this->get('Item');
        $this->state = $this->get('State');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            throw new \JViewGenericdataexception(implode("\n", $errors), 500);
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
        \JFactory::getApplication()->input->set('hidemainmenu', true);

        $user       = \JFactory::getUser();
        $userId     = $user->id;
        $isNew      = ($this->item->id == 0);
        $checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $userId);

        // Since we don't track these assets at the item level, use the category id.
        $canDo = \JHelperContent::getActions('com_jongman');

        \JToolbarHelper::title($isNew ? \JText::_('COM_JONGMAN_MANAGER_SCHEDULE_NEW') : \JText::_('COM_JONGMAN_MANAGER_SCHEDULE_EDIT'), 'bookmark schedules');

        $toolbarButtons = [];

        // If not checked out, can save the item.
        if (!$checkedOut && ($canDo->get('core.edit') || count($user->getAuthorisedCategories('com_jongman', 'core.create')) > 0))
        {
            $toolbarButtons[] = ['apply', 'schedule.apply'];
            $toolbarButtons[] = ['save', 'schedule.save'];

            if ($canDo->get('core.create'))
            {
                $toolbarButtons[] = ['save2new', 'schedule.save2new'];
            }
        }

        // If an existing item, can save to a copy.
        if (!$isNew && $canDo->get('core.create'))
        {
            $toolbarButtons[] = ['save2copy', 'schedule.save2copy'];
        }

        \JToolbarHelper::saveGroup(
            $toolbarButtons,
            'btn-success'
        );

        if (empty($this->item->id))
        {
            \JToolbarHelper::cancel('schedule.cancel');
        }
        else
        {
            if (\JComponentHelper::isEnabled('com_jongmanhistory') && $this->state->params->get('save_history', 0) && $canDo->get('core.edit'))
            {
                \JToolbarHelper::versions('com_jongman.schedule', $this->item->id);
            }

            \JToolbarHelper::cancel('schedule.cancel', 'JTOOLBAR_CLOSE');
        }

        \JToolbarHelper::divider();
        \JToolbarHelper::help('JHELP_COMPONENTS_JONGMAN_SCHEDULES_EDIT');
    }
}