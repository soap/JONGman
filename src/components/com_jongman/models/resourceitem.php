<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

// Base this model on the backend version.
JLoader::register('JongmanModelResource', JPATH_ADMINISTRATOR . '/components/com_jongman/models/resource.php');

/**
 * The Jongman ResourceForm model extends from backend Resource model.
 *
 * @package     JONGman
 * @subpackage  com_jongman
 * @since       1.0
 */
class JongmanModelResourceitem extends JongmanModelResource
{

	/**
	 * 
	 * Construnctor method
	 * Add path for forms and field to JForm class
	 * @param unknown_type $config
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);
		
		JForm::addFormPath(JPATH_ADMINISTRATOR . '/components/com_jongman/models/forms');
		JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_jongman/models/fields');
	}

	/**
	 * Method to get a ResourceForm.
	 *
	 * @param   integer  $pk  An optional id of the object to get, otherwise the id from the model state is used.
	 *
	 * @return  mixed    Category data object on success, false on failure.
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($result = parent::getItem($pk)) {
		
		}
		return $result;
	}
	
    public function getReturnPage()
    {
        return base64_encode($this->getState('return_page'));
    }

    /**
     * Method to auto-populate the model state.
     * Note. Calling getState in this method will result in recursion.
     *
     * @return    void
     */
    protected function populateState()
    {
        // Load state from the request.
        $pk = JRequest::getInt('id');
        $this->setState($this->getName() . '.id', $pk);

        $return = JRequest::getVar('return', null, 'default', 'base64');
        $this->setState('return_page', base64_decode($return));

        // Load the parameters.
        $params = JFactory::getApplication()->getParams();
        $this->setState('params', $params);

        $this->setState('layout', JFactory::getApplication()->input->get('layout', '', 'string'));
    }
}