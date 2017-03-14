<?php
defined('_JEXEC') or die();
jimport('joomla.application.component.view');
/**
 * Customer JSON list view class.
 *
 */
class JongmanViewCustomers extends JViewLegacy
{
    /**
     * Generates a list of JSON items.
     *
     * @return    void
     */
    function display($tpl = null)
    {
        $ta   = (int) JRequest::getUInt('typeahead');
        $s2   = (int) JRequest::getUInt('select2');
        $resp = array();

        // Get model data
        $rows = $this->get('Items');

        if ($ta) {
            $tmp_rows = array();

            foreach ($rows AS &$row)
            {
                $id = (int) $row->id;

                $tmp_rows[$id] = $this->escape($row->name.', '.$row->suburb.', '.$row->state.' '.$row->postcode.' '.$row->country);
            
            }

            $rows = $tmp_rows;
        }
        elseif ($s2) {
            $tmp_rows = array();

            foreach ($rows AS &$row)
            {
                $id = (int) $row->id;

                $item = new stdClass();
                $item->id = $id;
                $item->text = $this->escape($row->name.', '.$row->suburb.', '.$row->state.' '.$row->postcode.' '.$row->country);

                $tmp_rows[] = $item;
            }

            $rows  = $tmp_rows;
            $total = (int) $this->get('Total');
            $rows  = array('total' => $total, 'items' => $rows);
        }

        // Set the MIME type for JSON output.
        JFactory::getDocument()->setMimeEncoding('application/json');

        // Change the suggested filename.
        JResponse::setHeader('Content-Disposition', 'attachment;filename="' . $this->getName() . '.json"');

        // Output the JSON data.
        echo json_encode($rows);

        jexit();
    }
}
