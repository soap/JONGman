<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die();
jimport('joomla.application.component.view');
/**
 * Workflow JSON list view class.
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

                $tmp_rows[$id] = $this->escape($row->title);
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
                $item->text = $this->escape($row->title);

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
