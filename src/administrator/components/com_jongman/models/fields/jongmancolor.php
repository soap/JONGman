<?php
/**
* @package     Joomla Extensions
* @subpackage  JONGman
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die;

/**
 * Color Form Field class for the Joomla 
 * This implementation is designed to be compatible with HTML5's <input type="color">
 *
 */
class JFormFieldJongmanColor extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  11.3
	 */
	protected $type = 'JongmanColor';
	
	protected static $loaded = null;

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string  The field input markup.
	 *
	 * @since   11.3
	 */
	protected function getInput()
	{
		// Initialize some field attributes.
		$size = $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"' : '';
		$classes = (string) $this->element['class'];
		$disabled = ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		if (!$disabled)
		{
			self::loadColorPicker();
			$classes .= ' color-field';
		}

		if (empty($this->value))
		{
			// A color field can't be empty, we default to black. This is the same as the HTML5 spec.
			$this->value = '#000000';
		}

		// Initialize JavaScript field attributes.
		$onchange = $this->element['onchange'] ? ' onchange="' . (string) $this->element['onchange'] . '"' : '';

		$class = $classes ? ' class="' . trim($classes) . '"' : '';

		return '<input type="text" name="' . $this->name . '" id="' . $this->id . '"' . ' value="'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $onchange . '/>';
	}
	
	private function loadColorPicker()
	{
		if (empty(self::$loaded)) {
			// Include MooTools framework
			JHtml::_('behavior.framework');

			JFactory::getDocument()->addScript('/joomla_dev/media/com_jongman/colorpicker/DynamicColorPicker.js');
			JFactory::getDocument()
				->addScriptDeclaration(
				"window.addEvent('domready', function(){
					DynamicColorPicker.auto(\".color-field\");
				});
			"
			);
			self::$loaded = true;
		}
	}
}
