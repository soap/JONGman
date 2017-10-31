<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

namespace Soap\Component\Jongman\Administrator\Field\Modal;

defined('_JEXEC') or die;

use Joomla\CMS\Form\FormField;
use Joomla\CMS\Table\Table;
/**
 * Field to select a user id from a modal Jongman list.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       1.0
 */
class LayoutField extends FormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6.0
	 */
	public $type = 'Modal_Layout';

	/**
	 * Method to get the Layout field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   1.6.0
	 */
	protected function getInput()
	{
        $allowNew    = ((string) $this->element['new'] == 'true');
        $allowEdit   = ((string) $this->element['edit'] == 'true');
        $allowClear  = ((string) $this->element['clear'] != 'false');
        $allowSelect = ((string) $this->element['select'] != 'false');

        // Load language
        \JFactory::getLanguage()->load('com_jongman', JPATH_ADMINISTRATOR);

        // The active article id field.
        $value = (int) $this->value > 0 ? (int) $this->value : '';

        // Create the modal id.
        $modalId = 'Layout_' . $this->id;

        // Add the modal field script to the document head.
        \JHtml::_('jquery.framework');
        \JHtml::_('script', 'system/fields/modal-fields.min.js', array('version' => 'auto', 'relative' => true));

        // Script to proxy the select modal function to the modal-fields.js file.
        if ($allowSelect)
        {
            static $scriptSelect = null;

            if (is_null($scriptSelect))
            {
                $scriptSelect = array();
            }

            if (!isset($scriptSelect[$this->id]))
            {
                \JFactory::getDocument()->addScriptDeclaration("
				function jSelectLayout_" . $this->id . "(id, title, object, url, language) {
					window.processModalSelect('Layout', '" . $this->id . "', id, title, object, url, language);
				}
				");

                $scriptSelect[$this->id] = true;
            }
        }

        // Setup variables for display.
        $linkLayouts = 'index.php?option=com_jongman&amp;view=layouts&amp;layout=modal&amp;tmpl=component&amp;' . \JSession::getFormToken() . '=1';
        $linkLayout  = 'index.php?option=com_jongman&amp;view=layout&amp;layout=modal&amp;tmpl=component&amp;' . \JSession::getFormToken() . '=1';

        if (isset($this->element['language']))
        {
            $linkLayouts .= '&amp;forcedLanguage=' . $this->element['language'];
            $linkLayout  .= '&amp;forcedLanguage=' . $this->element['language'];
            $modalTitle    = \JText::_('COM_JONGMAN_CHANGE_LAYOUT') . ' &#8212; ' . $this->element['label'];
        }
        else
        {
            $modalTitle    = \JText::_('COM_JONGMAN_CHANGE_LAYOUT');
        }

        $urlSelect = $linkLayouts . '&amp;function=jSelectLayout_' . $this->id;
        $urlEdit   = $linkLayout . '&amp;task=layout.edit&amp;id=\' + document.getElementById("' . $this->id . '_id").value + \'';
        $urlNew    = $linkLayout . '&amp;task=layout.add';

        if ($value)
        {
            $db    = \JFactory::getDbo();
            $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__jongman_layouts'))
                ->where($db->quoteName('id') . ' = ' . (int) $value);
            $db->setQuery($query);

            try
            {
                $title = $db->loadResult();
            }
            catch (\RuntimeException $e)
            {
                \JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }

        $title = empty($title) ? \JText::_('COM_JONGMAN_SELECT_A_LAYOUT') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        // The current article display field.
        $html  = '';
        if ($allowSelect || $allowNew || $allowEdit || $allowClear)
        {
            $html .= '<span class="input-group">';
        }

        $html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $title . '" disabled="disabled" size="35">';

        if ($allowSelect || $allowNew || $allowEdit || $allowClear)
        {
            $html .= '<span class="input-group-btn">';
        }

        // Select article button
        if ($allowSelect)
        {
            $html .= '<a'
                . ' class="btn btn-primary hasTooltip' . ($value ? ' sr-only' : '') . '"'
                . ' id="' . $this->id . '_select"'
                . ' data-toggle="modal"'
                . ' role="button"'
                . ' href="#ModalSelect' . $modalId . '"'
                . ' title="' . \JHtml::tooltipText('COM_JONGMAN_CHANGE_LAYOUT') . '">'
                . '<span class="icon-file" aria-hidden="true"></span> ' . \JText::_('JSELECT')
                . '</a>';
        }

        // New article button
        if ($allowNew)
        {
            $html .= '<a'
                . ' class="btn btn-secondary hasTooltip' . ($value ? ' sr-only' : '') . '"'
                . ' id="' . $this->id . '_new"'
                . ' data-toggle="modal"'
                . ' role="button"'
                . ' href="#ModalNew' . $modalId . '"'
                . ' title="' . \JHtml::tooltipText('COM_JONGMAN_NEW_LAYOUT') . '">'
                . '<span class="icon-new" aria-hidden="true"></span> ' . \JText::_('JACTION_CREATE')
                . '</a>';
        }

        // Edit article button
        if ($allowEdit)
        {
            $html .= '<a'
                . ' class="btn btn-secondary hasTooltip' . ($value ? '' : ' sr-only') . '"'
                . ' id="' . $this->id . '_edit"'
                . ' data-toggle="modal"'
                . ' role="button"'
                . ' href="#ModalEdit' . $modalId . '"'
                . ' title="' . \JHtml::tooltipText('COM_JONGMAN_EDIT_LAYOUT') . '">'
                . '<span class="icon-edit" aria-hidden="true"></span> ' . \JText::_('JACTION_EDIT')
                . '</a>';
        }

        // Clear article button
        if ($allowClear)
        {
            $html .= '<a'
                . ' class="btn btn-secondary' . ($value ? '' : ' sr-only') . '"'
                . ' id="' . $this->id . '_clear"'
                . ' href="#"'
                . ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
                . '<span class="icon-remove" aria-hidden="true"></span>' . \JText::_('JCLEAR')
                . '</a>';
        }

        if ($allowSelect || $allowNew || $allowEdit || $allowClear)
        {
            $html .= '</span></span>';
        }

        // Select article modal
        if ($allowSelect)
        {
            $html .= \JHtml::_(
                'bootstrap.renderModal',
                'ModalSelect' . $modalId,
                array(
                    'title'       => $modalTitle,
                    'url'         => $urlSelect,
                    'height'      => '400px',
                    'width'       => '800px',
                    'bodyHeight'  => 70,
                    'modalWidth'  => 80,
                    'footer'      => '<a role="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">'
                        . \JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>',
                )
            );
        }

        // New article modal
        if ($allowNew)
        {
            $html .= \JHtml::_(
                'bootstrap.renderModal',
                'ModalNew' . $modalId,
                array(
                    'title'       => \JText::_('COM_CONTENT_NEW_ARTICLE'),
                    'backdrop'    => 'static',
                    'keyboard'    => false,
                    'closeButton' => false,
                    'url'         => $urlNew,
                    'height'      => '400px',
                    'width'       => '800px',
                    'bodyHeight'  => 70,
                    'modalWidth'  => 80,
                    'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'layout\', \'cancel\', \'item-form\'); return false;">'
                        . \JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
                        . '<a role="button" class="btn btn-primary" aria-hidden="true"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'layout\', \'save\', \'item-form\'); return false;">'
                        . \JText::_('JSAVE') . '</a>'
                        . '<a role="button" class="btn btn-success" aria-hidden="true"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'layout\', \'apply\', \'item-form\'); return false;">'
                        . \JText::_('JAPPLY') . '</a>',
                )
            );
        }

        // Edit article modal
        if ($allowEdit)
        {
            $html .= \JHtml::_(
                'bootstrap.renderModal',
                'ModalEdit' . $modalId,
                array(
                    'title'       => \JText::_('COM_CONTENT_EDIT_ARTICLE'),
                    'backdrop'    => 'static',
                    'keyboard'    => false,
                    'closeButton' => false,
                    'url'         => $urlEdit,
                    'height'      => '400px',
                    'width'       => '800px',
                    'bodyHeight'  => 70,
                    'modalWidth'  => 80,
                    'footer'      => '<a role="button" class="btn btn-secondary" aria-hidden="true"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'layout\', \'cancel\', \'item-form\'); return false;">'
                        . \JText::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</a>'
                        . '<a role="button" class="btn btn-primary" aria-hidden="true"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'layout\', \'save\', \'item-form\'); return false;">'
                        . \JText::_('JSAVE') . '</a>'
                        . '<a role="button" class="btn btn-success" aria-hidden="true"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'layout\', \'apply\', \'item-form\'); return false;">'
                        . \JText::_('JAPPLY') . '</a>',
                )
            );
        }

        // Note: class='required' for client side validation.
        $class = $this->required ? ' class="required modal-value"' : '';

        $html .= '<input type="hidden" id="' . $this->id . '_id" ' . $class . ' data-required="' . (int) $this->required . '" name="' . $this->name
            . '" data-text="' . htmlspecialchars(\JText::_('COM_JONGMAN_SELECT_A_LAYOUT', true), ENT_COMPAT, 'UTF-8') . '" value="' . $value . '">';

        return $html;
    }
}