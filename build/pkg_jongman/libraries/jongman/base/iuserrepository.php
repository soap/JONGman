<?php
defined('_JEXEC') or die;

interface IUserRepository
{
	public function loadById($userId); 
}