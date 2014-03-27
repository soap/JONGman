<?php
defined('_JEXEC') or die;

class JongmanControllerPostinstall extends JControllerLegacy
{
	public function getModel($name = '', $prefix = '', $config = array()) 
	{
		return parent::getModel('Postinstall', 'JongmanModel');	
	}
	
	/**
	 * 
	 * Install sample data upon request
	 * Return json data to the client browser
	 */
	public function install()
	{
		$data = array();
		$model = $this->getModel();
		if (!$model) {
			$data['success'] = false;
			$data['message'] = JText::_('COM_JONGMAN_ERROR_LOADING_POSTINSTALL_MODEL');	
		}else {
			if ($model->install()) {
				$data['success'] = true;
				$data['message'] = '';		
			}else{
				$data['success'] = false;
				$data['message'] = $model->getError();
			}	
		}
		
		$document = JFactory::getDocument();
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="'.$this->getName().'.json"');
		
		echo json_encode($data);		
		jexit();
	}
	
	public function verify()
	{
		$data = array();
		$model = $this->getModel();
		if ($model->installed()) {
			$data['success'] = true;
		}else{
			$data['success'] = false;
		}	
		
		$document = JFactory::getDocument();
		// Set the MIME type for JSON output.
		$document->setMimeEncoding('application/json');
		// Change the suggested filename.
		JResponse::setHeader('Content-Disposition','attachment;filename="'.$this->getName().'.json"');
		
		echo json_encode($data);		
		jexit();			
	}
	/**
	 *
	 * Cancel post installation operation and return to component dashboard
	 */
	public function cancel()
	{
		$this->setRedirect('index.php?option=com_jongman');
		return true;
	}
}