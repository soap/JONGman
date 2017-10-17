<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/

defined('_JEXEC') or die();


jimport('joomla.application.component.helper');


/**
 * Utility class for Projectfork javascript behaviors
 *
 */
abstract class JMhtmlScript
{
    /**
     * Array containing information for loaded files
     *
     * @var    array    $loaded
     */
    protected static $loaded = array();


    /**
     * Method to load jQuery JS
     *
     * @return    void
     */
    public static function jQuery()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }
		$component = JFactory::getApplication()->input->get('option');
        $params = JComponentHelper::getParams($component);

        if (JFactory::getApplication()->isSite()) {
            $load = $params->get('jquery_site');
        }
        else {
            $load = $params->get('jquery_admin');
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html' && $load != '0') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptjQuery');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load jQuery UI JS
     *
     * @return    void
     */
    public static function jQueryUI()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load dependencies
        if (empty(self::$loaded['jQuery'])) {
            self::jQuery();
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptjQueryUI');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load jQuery Sortable JS
     *
     * @return    void
     */
    public static function jQuerySortable()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load dependencies
        if (empty(self::$loaded['jQueryUI'])) {
            self::jQueryUI();
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptjQuerySortable');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load jQuery Chosen JS
     *
     * @return    void
     */
    public static function jQueryChosen()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load dependencies
        if (empty(self::$loaded['jQuery'])) {
            self::jQuery();
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptjQueryChosen');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load jQuery Select2 JS
     *
     * @return    void
     */
    public static function jQuerySelect2()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load dependencies
        if (empty(self::$loaded['jQuery'])) {
            self::jQuery();
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptjQuerySelect2');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load bootstrap JS
     *
     * @return    void
     */
    public static function bootstrap()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load dependencies
        if (empty(self::$loaded['jQuery'])) {
            self::jQuery();
        }
        
		$component = JFactory::getApplication()->input->get('option');
        $params = JComponentHelper::getParams($component);

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html' && $params->get('bootstrap_js') != '0') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptBootstrap');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load jQuery flot JS
     *
     * @return    void
     */
    public static function flot()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load dependencies
        if (empty(self::$loaded['jQuery'])) {
            self::jQuery();
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptFlot');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load Projectfork form JS
     *
     * @return    void
     */
    public static function form()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load dependencies
        if (empty(self::$loaded['jQuery'])) {
            self::jQuery();
        }

        if (empty(self::$loaded['jongman'])) {
            self::jongman();
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptForm');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load Projectfork list form JS
     *
     * @return    void
     */
    public static function listForm()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load dependencies
        if (empty(self::$loaded['jQuery'])) {
            self::jQuery();
        }

        if (empty(self::$loaded['jongman'])) {
            self::jongman();
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptListForm');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load upload JS
     *
     * @return    void
     */
    public static function upload()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerProjectforkScriptUpload');
        }

        self::$loaded[__METHOD__] = true;
    }


    /**
     * Method to load Projectfork base JS
     *
     * @return    void
     */
    public static function jongman()
    {
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }

        // Load only of doc type is HTML
        if (JFactory::getDocument()->getType() == 'html') {
            $dispatcher	= JDispatcher::getInstance();
            $dispatcher->register('onBeforeCompileHead', 'triggerJongmanScriptCore');
        }

        self::$loaded[__METHOD__] = true;
    }
}


/**
 * Stupid but necessary way of adding jQuery to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that jQuery is not already loaded
 *
 */
function triggerJongmanScriptjQuery()
{
    $params = JComponentHelper::getParams('com_jongman');

    if (JFactory::getApplication()->isSite()) {
        $load = $params->get('jquery_site');
    }
    else {
        $load = $params->get('jquery_admin');
    }

    // Auto-load
    if ($load == '') {
        $scripts = (array) array_keys(JFactory::getDocument()->_scripts);
        $string  = implode('', $scripts);

        if (stripos($string, 'jquery') === false) {
            JHtml::_('script', 'com_jongman/jquery/jquery.min.js', false, true, false, false, false);
            JHtml::_('script', 'com_jongman/jquery/jquery.noconflict.js', false, true, false, false, false);
        }
    }

    // Force load
    if ($load == '1') {
        JHtml::_('script', 'com_jongman/jquery/jquery.min.js', false, true, false, false, false);
        JHtml::_('script', 'com_jongman/jquery/jquery.noconflict.js', false, true, false, false, false);
    }
}


/**
 * Stupid but necessary way of adding jQuery UI to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that flot is loaded after jQuery
 *
 */
function triggerProjectforkScriptjQueryUI()
{
    $scripts = (array) array_keys(JFactory::getDocument()->_scripts);
    $string  = implode('', $scripts);

    if (stripos($string, 'jquery.ui') === false) {
        JHtml::_('script', 'com_jongman/jquery/jquery.ui.core.min.js', false, true, false, false, false);
    }
}


/**
 * Stupid but necessary way of adding jQuery Chosen to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that the script is loaded after jQuery
 *
 */
function triggerProjectforkScriptjQueryChosen()
{
    if (version_compare(JVERSION, '3', 'ge')) {
        JHtml::_('script', 'jui/chosen.jquery.min.js', false, true, false, false, false);
		JHtml::_('stylesheet', 'jui/chosen.css', false, true);
    }
    else {
        JHtml::_('script', 'com_jongman/chosen/chosen.jquery.min.js', false, true, false, false, false);
        JHtml::_('stylesheet', 'com_jongman/chosen/chosen.css', false, true);
    }
}


/**
 * Stupid but necessary way of adding jQuery Select2 to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that the script is loaded after jQuery
 *
 */
function triggerJongmanScriptjQuerySelect2()
{
    JHtml::_('script', 'com_jongman/select2/select2.min.js', false, true, false, false, false);
    JHtml::_('stylesheet', 'com_jongman/select2/select2.css', false, true);
}


/**
 * Stupid but necessary way of adding jQuery Sortable to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that flot is loaded after jQuery
 *
 */
function triggerJongmanScriptjQuerySortable()
{
    $scripts = (array) array_keys(JFactory::getDocument()->_scripts);
    $string  = implode('', $scripts);

    if (stripos($string, 'jquery.ui.sortable') === false) {
        JHtml::_('script', 'com_jongman/jquery/jquery.ui.sortable.min.js', false, true, false, false, false);
    }
}


/**
 * Stupid but necessary way of adding Bootstrap JS to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that Bootstrap JS is not already loaded
 *
 */
function triggerJongmanScriptBootstrap()
{
    $params = JComponentHelper::getParams('com_jongman');

    $load = $params->get('bootstrap_js');

    // Auto-load
    if ($load == '') {
        $scripts = (array) array_keys(JFactory::getDocument()->_scripts);
        $string  = implode('', $scripts);

        if (stripos($string, 'bootstrap') === false) {
            JHtml::_('script', 'com_jongman/bootstrap/bootstrap.min.js', false, true, false, false, false);
        }
    }

    // Force load
    if ($load == '1') {
        JHtml::_('script', 'com_jongman/bootstrap/bootstrap.min.js', false, true, false, false, false);
    }
}


/**
 * Stupid but necessary way of adding jQuery Flot to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that flot is loaded after jQuery
 *
 */
function triggerJongmanScriptFlot()
{
    JHtml::_('script', 'com_jongman/flot/jquery.flot.min.js', false, true, false, false, false);
    JHtml::_('script', 'com_jongman/flot/jquery.flot.pie.min.js', false, true, false, false, false);
    JHtml::_('script', 'com_jongman/flot/jquery.flot.resize.min.js', false, true, false, false, false);
}

/**
 * Stupid but necessary way of adding PF form JS to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that the form JS is loaded after jQuery
 *
 */
function triggerJongmanScriptForm()
{
    JHtml::_('script', 'com_jongman/jongman/form.js', false, true, false, false, false);
}


/**
 * Stupid but necessary way of adding JM list form JS to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that the list form JS is loaded after jQuery
 *
 */
function triggerJongmanScriptListForm()
{
    JHtml::_('script', 'com_jongman/jongman/list.js', false, true, false, false, false);
}


function triggerJongmanScriptUpload()
{
    JHtml::_('script', 'com_jongman/jongman/upload.js', false, true, false, false, false);
}


/**
 * Stupid but necessary way of adding JM core JS to the document head.
 * This function is called by the "onCompileHead" system event and makes sure that the core JS is loaded after jQuery
 *
 */
function triggerJongmanScriptCore()
{
    JHtml::_('script', 'com_jongman/jongman/jongman.js', false, true, false, false, false);
}

