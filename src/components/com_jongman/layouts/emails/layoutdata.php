<?php
/**
* @package     JONGman Package
*
* @copyright   Copyright (C) 2005 - 2017 Prasit Gebsaap, Inc. All rights reserved.
* @license     GNU General Public License version 2 or later; see LICENSE.txt
*/
defined('_JEXEC') or die;

class RFLayoutData {
	
	private $instance;
	private $transition;
	private $sourceState;
	private $targetState;
	private $commonStrings = array();
	protected $replacements = array();
	
	public function __construct($oInstance, $oTransition, $oSourceState, $oTargetState, $oDocument, $oUser, $comment) 
	{
		$lang = JFactory::getLanguage();
		$lang->load('com_jongman');
		
		$this->instance		= $oInstance;
		$this->transition 	= $oTransition;
		$this->sourceState	= $oSourceState;
		$this->targetState  = $oTargetState;
		$this->document		= $oDocument;
		$this->user			= $oUser;
		$this->comment		= $comment;
	}
	
	public function getDisplayData($key)
	{
			
	}
	
	public function getReplacementPairs($key)
	{
		if (empty($this->replacements)) $this->buildReplacements();
		return array_merge($this->replacements[$key], $this->commonStrings);
	}
	
	protected function buildReplacements()
	{
		$this->commonStrings = array(			
			'HELLO'=>JText::_('COM_JONGMAN_EMAIL_HELLO'),
			'REGARDS'=>JText::_('COM_JONGMAN_EMAIL_REGARDS'),
			'SYSTEM_ADMIN'=>JText::_('COM_JONGMAN_EMAIL_SYSTEM_ADMIN')
		);
			
		$this->replacements['notify_actor'] = array(

			'ACTOR_NOTIFICATION'=>JText::_('COM_JONGMAN_EMAIL_ACTOR_NOTIFICATION'),
			'YOU_HAVE_MADE_TRANSITION_ON_DOCUMENT'=>JText::_('COM_JONGMAN_EMAIL_YOU_MADE_A_TRANSITION')		
		);
		
		$this->replacements['notify_authors'] = array(
			'AUTHOR_NOTIFICATION'=>JText::_('COM_JONGMAN_EMAIL_AUTHOR_NOTIFICATION'),
			'YOUR_DOCUMENT_HAS_BEEN_TRANSITIONED'=>JText::sprintf('COM_JONGMAN_EMAIL_DOCUMENT_WAS_TRANSITIONED_BY', $this->transition->title, $this->user->name)
		);
		
		$this->replacements['notify_next_actors'] = array(
			'NEXT_ACTORS_NOTIFICATION'=>JText::_('COM_JONGMAN_EMAIL_NEXT_ACTORS_NOTIFICATION'),
			'DOCUMENT_WAITING_FOR_YOUR_CONSIDERATION'=>JText::_('COM_JONGMAN_EMAIL_DOCUMENT_WAIT_FOR_YOU')
		);
		
	}
}