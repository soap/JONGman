<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

jimport('joomla.form.helper');


class JFormFieldJongmanSelectTimespan extends JFormField {

    protected $type = 'JongmanSelectTimespan';

    public function getInput() {
        // Initialize variables.
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';

		// To avoid user's confusion, readonly="true" should imply disabled="true".
		if ( (string) $this->element['readonly'] == 'true' || (string) $this->element['disabled'] == 'true') {
			$attr .= ' disabled="disabled"';
		}

		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';
		$attr .= $this->multiple ? ' multiple="multiple"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true') {
			$html[] = JHtml::_('select.genericlist', $options, '', trim($attr), 'value', 'text', $this->value, $this->id);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);
		}

		return implode($html);
    }

    protected function getOptions() {
        $options = array();
        $options[] = JHtml::_('select.option', '10', '10 '.JText::_('COM_JONGMAN_MINUTES'));
        $options[] = JHtml::_('select.option', '15', '15 '.JText::_('COM_JONGMAN_MINUTES'));
		$options[] = JHtml::_('select.option', '30', '30 '.JText::_('COM_JONGMAN_MINUTES'));
		$options[] = JHtml::_('select.option', '60', '1 '.JText::_('COM_JONGMAN_HOUR'));
		$options[] = JHtml::_('select.option', '120', '2 '.JText::_('COM_JONGMAN_HOURS'));
        $options[] = JHtml::_('select.option', '180', '3 '.JText::_('COM_JONGMAN_HOURS'));
		$options[] = JHtml::_('select.option', '240', '4 '.JText::_('COM_JONGMAN_HOURS'));
		$options[] = JHtml::_('select.option', '360', '6 '.JText::_('COM_JONGMAN_HOURS'));
		$options[] = JHtml::_('select.option', '480', '8 '.JText::_('COM_JONGMAN_HOURS'));
		$options[] = JHtml::_('select.option', '720', '12 '.JText::_('COM_JONGMAN_HOURS'));
        $options[] = JHtml::_('select.option', '1440', '24 '.JText::_('COM_JONGMAN_HOURS'));

        return $options;
    }

}