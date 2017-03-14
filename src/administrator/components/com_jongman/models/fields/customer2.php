<?php
/**
 * @package      JONGman
*
* @author       Prasit Gebsaap (Soap)
* @copyright    Copyright (C) 2006-2015 Prasit Gebsaap. All rights reserved.
* @license      http://www.gnu.org/licenses/gpl.html GNU/GPL, see LICENSE.txt
*/

defined('_JEXEC') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');

/**
 * Form Field class for selecting a project.
 *
*/
class JFormFieldCustomer2 extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 */
	public $type = 'Customer';


	/**
	 * Method to get the field input markup.
	 *
	 * @return    string    The html field markup
	 */
	protected function getInput()
	{
		// Load the modal behavior script
		JHtml::_('behavior.modal', 'a.modal_' . $this->id);

		// Add the script to the document head.
		$script = $this->getJavascript();
		JFactory::getDocument()->addScriptDeclaration(implode("\n", $script));

		// Load the current project title a value is set.
		$title = ($this->value ? $this->getCustomerTitle() : JText::_('COM_JONGMAN_SELECT_A_CUSTOMER'));

		if ($this->value == 0) $this->value = '';

		$html = $this->getHTML($title);

		return implode("\n", $html);
	}


	/**
	 * Method to generate the input markup.
	 *
	 * @param     string    $title    The title of the current value
	 *
	 * @return    string              The html field markup
	 */
	protected function getHTML($title)
	{
		if (JFactory::getApplication()->isSite() || version_compare(JVERSION, '3.0.0', 'ge')) {
			return $this->getHTMLSelect2($title);
		}

		return $this->getAdminHTML($title);
	}


	protected function getHTMLSelect2($title, $debug=null)
	{
		$user = JFactory::getUser();
		$can_add  = $user->authorise('core.create', 'com_jongman');
		$can_edit = $user->authorise('core.edit', 'com_jongman');
		if ($debug==null) {
			$config = JFactory::getConfig();
			$debug  = (boolean) $config->get('debug');
		}
			
		JHtml::_('jquery.framework');
		JHtml::_('script', 'com_jongman/select2/select2.min.js', false, true, false, false, false);
		JHtml::_('stylesheet', 'com_jongman/select2/select2.css', false, true);

		static $field_id = 0;

		$field_id++;

		$doc = JFactory::getDocument();
		$app = JFactory::getApplication();

		// Prepare field attributes
		$editLink = 'index.php?option=com_jongman&task=customer.edit&tmpl=component&modal=1&id=';
		$changes = array();
		$changes[] = "var el = jQuery('#{$this->id}_edit');";
		$changes[] = "el.attr(\"href\", '{$editLink}'+jQuery('#{$this->id}_id').val());";
		$changes[] = "if (el.attr('disabled')) el.removeAttr('disabled');";
		//$changes[] = "console.log('customer id changed....disabled attribute is '+el.attr('disabled'));";
		$change = implode("\n", $changes);
		
		$can_change = isset($this->element['readonly']) ? (bool) $this->element['readonly'] : true;
		if ($can_edit) {
			$onchange   = isset($this->element['onchange']) ? $change.$this->element['onchange'] : $change;
		}else{
			$onchange   = isset($this->element['onchange']) ? $this->element['onchange'] : '';
		}
		$attr_read  = ($can_change ? '' : ' readonly="readonly"');
		$css_txt    = ($can_change ? '' : ' disabled muted') . (!empty($value) ? ' success' : ' warning');
		$value      = (int) $this->value;
		$placehold  = htmlspecialchars(JText::_('COM_JONGMAN_SELECT_CUSTOMER'), ENT_COMPAT, 'UTF-8');
		$title      = htmlspecialchars($title, ENT_COMPAT, 'UTF-8');

		if (empty($title)) {
			$title = $placehold;
		}

		// Query url
		$url = 'index.php?option=com_jongman&view=customers&tmpl=component&format=json&select2=1';

		// Prepare JS select2 script
		$js = array();
		$js[] = "jQuery(document).ready(function()";
		$js[] = "{";
		$js[] = "    jQuery('#" . $this->id . "_id').select2({";
		$js[] = "        placeholder: '" . $placehold . "',";
		if ($value) $js[] = "        allowClear: true,";
		$js[] = "        minimumInputLength: 0,";
		$js[] = "        ajax: {";
		$js[] = "            url: '" . $url . "',";
		$js[] = "            dataType: 'json',";
		$js[] = "            quietMillis: 200,";
		$js[] = "            data: function (term, page) {return {filter_search: term, limit: 10, limitstart: ((page - 1) * 10)};},";
		$js[] = "            results: function (data, page) {var more = (page * 10) < data.total;return {results: data.items, more: more};}";
		$js[] = "        },";
		$js[] = "        escapeMarkup:function(markup) { return markup; },";
		$js[] = "        initSelection: function(element, callback) {";
		$js[] = "           callback({id:" . $value . ", text: '" . htmlspecialchars($title, ENT_QUOTES) . "'});";
		$js[] = "        }";
		$js[] = "    });";
		// on change for hidden field storing customer id
		$js[] = "    jQuery('#" . $this->id . "_id').change(function(){" . $onchange . "});";
		$js[] = "});";

		// Prepare html output
		$html = array();

		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" placeholder="' . $title . '"';
		$html[] = ' value="' . $value . '" autocomplete="off"' . $attr_read . ' class="input-large" tabindex="-1" />';
		
		if ($can_change) {
			$newLink = 'index.php?option=com_jongman&task=customer.add&tmpl=component&modal=1';
			if ($can_add) {
				$html[] = '<a class="btn btn-sm btn-success modal_' . $this->id . ' btn" title="' . JText::_('COM_JONGMAN_ADD_CUSTOMER') . '"'
					. ' href="' . JRoute::_($newLink, false) . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
				$html[] = JText::_('COM_JONGMAN_ACTION_NEW') . '</a>';
			}
			if ($can_edit) {
				$disabled = '';
				if ($value == 0) $disabled = ' disabled="true"'; 
				$html[] = '<a id="'.$this->id.'_edit" class="btn btn-sm btn-success modal_' . $this->id . ' btn" title="' . JText::_('COM_JONGMAN_EDIT_CUSTOMER') . '"'
					.$disabled. ' href="' . JRoute::_($editLink.$value) . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
				$html[] = JText::_('COM_JONGMAN_ACTION_EDIT') . '</a>';
			}
			// Add script
			JFactory::getDocument()->addScriptDeclaration(implode("\n", $js));
		}

		return $html;
	}

	/**
	 * Method to generate the backend input markup.
	 *
	 * @param     string    $title    The title of the current value
	 *
	 * @return    array     $html     The html field markup
	 */
	protected function getAdminHTML($title)
	{
		$html = array();
		$link = 'index.php?option=com_jongman&amp;view=customers'
				. '&amp;layout=modal&amp;tmpl=component'
						. '&amp;function=jmSelectCustomer_' . $this->id;

		// Initialize some field attributes.
		$attr = $this->element['class'] ? ' class="' . (string) $this->element['class'] . '"' : '';
		$attr .= $this->element['size'] ? ' size="' . (int) $this->element['size'] . '"'      : '';

		// Create a dummy text field with the project title.
		$html[] = '<div class="fltlft">';
		$html[] = '    <input type="text" id="' . $this->id . '_name" value="' . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . '" disabled="disabled"' . $attr . ' />';
		$html[] = '</div>';

		// Create the customer select button.
		if ($this->element['readonly'] != 'true') {
			$html[] = '<div class="button2-left">';
			$html[] = '    <div class="blank">';
			$html[] = '<a class="modal_' . $this->id . '" title="' . JText::_('COM_JONGMAN_SELECT_CUSTOMER') . '"'
					. ' href="' . $link . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = JText::_('COM_JONGMAN_SELECT_CUSTOMER') . '</a>';
			$html[] = '    </div>';
			$html[] = '</div>';
		}

		// Create the hidden field, that stores the id.
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '" />';

		return $html;
	}


	/**
	 * Method to generate the frontend input markup.
	 *
	 * @param     string    $title    The title of the current value
	 *
	 * @return    array     $html     The html field markup
	 */
	protected function getSiteHTML($title)
	{
		$html = array();
		$isJ3 = version_compare(JVERSION, '3.0.0', 'ge');

		if (JFactory::getApplication()->isSite()) {
			$link = 'index.php?option=com_jongman&view=customers'
			. '&amp;layout=modal&amp;tmpl=component'
					. '&amp;function=jmSelectCustomer_' . $this->id;
		}
		else {
			$link = 'index.php?option=com_jongman&amp;view=customers'
					. '&amp;layout=modal&amp;tmpl=component'
							. '&amp;function=jmSelectCustomer_' . $this->id;
		}

		// Initialize some field attributes.
		$attr  = $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= $this->element['size']  ? ' size="'.(int) $this->element['size'].'"'      : '';

		if ($isJ3) {
			$html[] = '<div class="input-append">';
		}
		// Create a dummy text field with the customer title.
		$html[] = '<input type="text" id="' . $this->id . '_name" value="' . htmlspecialchars($title, ENT_COMPAT, 'UTF-8') . '" disabled="disabled"' . $attr . ' />';

		// Create the customer select button.
		if ($this->element['readonly'] != 'true') {
			$html[] = '<a class="modal_' . $this->id . ' btn" title="' . JText::_('COM_JONGMAN_SELECT_CUSTOMER') . '"'
					. ' href="' . JRoute::_($link) . '" rel="{handler: \'iframe\', size: {x: 800, y: 500}}">';
			$html[] = JText::_('COM_JONGMAN_SELECT_CUSTOMER') . '</a>';
		}

		if ($isJ3) {
			$html[] = '</div>';
		}

		// Create the hidden field, that stores the id.
		$html[] = '<input type="hidden" id="' . $this->id . '_id" name="' . $this->name . '" value="' . (int) $this->value . '" />';

		return $html;
	}


	/**
	 * Generates the javascript needed for this field
	 *
	 * @param     boolean    $submit    Whether to submit the form or not
	 * @param     string     $view      The name of the view
	 *
	 * @return    array      $script    The generated javascript
	 */
	protected function getJavascript()
	{
		$script   = array();
		
		$editLink = 'index.php?option=com_jongman&task=customer.edit&tmpl=component&modal=1&id=';
		$change = "jQuery('#{$this->id}_edit').attr('href', '{$editLink}'+jQuery('#{$this->id}_id').val());";
		$can_edit = JFactory::getUser()->authorise('core.edit', 'com_jongman');
		if ($can_edit) {
			$onchange   = isset($this->element['onchange']) ? $change.$this->element['onchange'] : $change;
		}else{
			$onchange   = isset($this->element['onchange']) ? $this->element['onchange'] : '';
		}
		

		$script[] = 'function jSelectCustomer_' . $this->id . '(nid, title)';
		$script[] = '{';
		$script[] = '    var old_id = document.getElementById("' . $this->id . '_id").value;';
		//$script[] = '     if (old_id != nid) {';
		$script[] = '         document.getElementById("' . $this->id . '_id").value = nid;';
		
		if (JFactory::getApplication()->isSite() || version_compare(JVERSION, '3.0.0', 'ge')) {
			$script[] = 'jQuery("#'. $this->id . '_id").val(nid).trigger("change");';
		}else{
			$script[] = '         document.getElementById("' . $this->id . '_name").value = title;';
		}
		$script[] = '         SqueezeBox.close(); ';
		$script[] = '         ' . $onchange;
		//$script[] = '     }';
		$script[] = '}';

		return $script;
	}


	/**
	 * Method to get the title of the currently selected project
	 *
	 * @return    string    The project title
	 */
	protected function getCustomerTitle()
	{
		$default = JText::_('COM_JONGMAN_SELECT_A_CUSTOMER');

		if (empty($this->value)) {
			return $default;
		}

		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('name, suburb, state, country, postcode')
			->from('#__jongman_customers')
			->where('id = ' . $db->quote($this->value));

		$db->setQuery((string) $query);
		$row = $db->loadObject();
		$title = $row->name.', '.$row->suburb.', '.$row->state.', '.$row->postcode.' '.$row->country;

		if (empty($title)) {
			return $default;
		}

		return $title;
	}
}