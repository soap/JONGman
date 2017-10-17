<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class JFormFieldReloadable extends JFormField
{
	protected $renderLayout = 'form.reloadablefield';
	
	public function renderField($options = array())
	{
		if ($this->hidden)
		{
			return $this->getInput();
		}
	
		if (!isset($options['class']))
		{
			$options['class'] = '';
		}
	
		$options['rel'] = '';
	
		if (empty($options['hiddenLabel']) && $this->getAttribute('hiddenLabel'))
		{
			$options['hiddenLabel'] = true;
		}
	
		if ($showon = $this->getAttribute('showon'))
		{
			$showon   = explode(':', $showon, 2);
			$options['class'] .= ' showon_' . implode(' showon_', explode(',', $showon[1]));
			$id = $this->getName($showon[0]);
			$options['rel'] = ' rel="showon_' . $id . '"';
			$options['showonEnabled'] = true;
		}
		
		$basePath = JPATH_COMPONENT.'/layouts';
		$displayData = array('input' => $this->getInput(), 'label' => $this->getLabel(), 'id'=>$this->id, 'options' => $options);
		
		return JLayoutHelper::render($this->renderLayout, $displayData, $basePath);
	}
}