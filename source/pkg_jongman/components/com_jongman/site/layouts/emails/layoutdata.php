<?php
defined('_JEXEC') or die;

class RFLayoutData {
	
	private $instance;
	private $transition;
	private $sourceState;
	private $targetState;
	private $document;
	private $user;
	private $comment;
	
	protected $data = array();
	protected $replacements = array();
	
	public function __construct($oInstance, $oTransition, $oSourceState, $oTargetState, $oDocument, $oUser, $comment) 
	{
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
		return $this->replacements[$key];
	}
	
	protected function buildReplacements()
	{
		$this->replacements['notify_actor'] = array(
				
		);
		
		$this->replacements['notify_authors'] = array(
				
		);
		
		$this->replacements['notify_next_actors'] = array(
				
		);
	}
}