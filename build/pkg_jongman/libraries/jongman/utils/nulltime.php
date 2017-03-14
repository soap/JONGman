<?php
defined('_JEXEC') or die;

class RFNullTime extends RFTime
{
	public function __construct()
	{
		parent::__construct(0, 0, 0, null);
	}
	
	public function toDatabase()
	{
		return null;
	}
	
	public function toString()
	{
		return '';
	}
}
