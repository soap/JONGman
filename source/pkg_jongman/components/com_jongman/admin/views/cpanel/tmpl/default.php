<?php defined('_JEXEC') or die;

if ($this->is_j25) :
	echo $this->loadTemplate('j25');
else:
	echo $this->loadTemplate('j3x');
endif;