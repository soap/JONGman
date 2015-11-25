<?php
defined('_JEXEC') or die;
$calendarType = $this->calendar->getType();
echo $this->loadTemplate($calendarType);