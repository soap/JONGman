<?php
defined('_JEXEC') or die;

class RFReservationStatus 
{
	const created = 1;
	const deleted = -2;
	const pending = 0;
	const approved = 2;
	const rejected = -1;	
}