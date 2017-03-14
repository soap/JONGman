<?php
defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Form Field class for selecting a Customer
 *
 */
class JFormFieldCustomer extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var    string
     */
    public $type = 'Customer';


    /**
     * Method to get the field list options markup.
     *
     * @return    array      $options      The list options markup.
     */
    protected function getOptions()
    {
        $options = array();
        $user    = JFactory::getUser();
        $db      = JFactory::getDbo();
        $query   = $db->getQuery(true);


        // Get field attributes for the database query
        $state = ($this->element['state']) ? (int) $this->element['state'] : NULL;

        // Build the query
        $query->select('a.id AS value, a.name as text')
              ->from('#__jongman_customers AS a');

        // Implement View Level Access.
        if (!$user->authorise('core.admin')) {
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN (' . $groups . ')');
        }

        // Filter by state
        if (!is_null($state)) $query->where('a.published = ' . $db->quote($state));

        $query->order('a.name');

        $db->setQuery((string) $query);
        $items = (array) $db->loadObjectList();

        // Generate the options
        if (count($items) > 0) {
            $options[] = JHtml::_('select.option', '',
                JText::alt('COM_JONGMAN_OPTION_SELECT_CUSTOMER',
                preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)),
                'value',
                'text'
            );
        }

        foreach($items AS $item)
        {
            // Create a new option object based on the <option /> element.
            $opt = JHtml::_('select.option', (string) $item->value,
                JText::alt(trim((string) $item->text),
                preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)),
                'value',
                'text'
            );

            // Add the option object to the result set.
            $options[] = $opt;
        }

        reset($options);

        return $options;
    }
}

