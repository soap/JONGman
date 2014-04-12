<?php
defined('_JEXEC') or die;

$function	= JRequest::getCmd('function', 'jSelectSchedule');
$list_order  = $this->escape($this->state->get('list.ordering'));
$list_dir    = $this->escape($this->state->get('list.direction'));

$sort_fields = $this->getSortFields();