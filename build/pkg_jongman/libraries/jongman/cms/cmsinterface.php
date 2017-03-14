<?php
defined('_JEXEC') or die;

interface IParamsService 
{
	function get($name, $default);
}