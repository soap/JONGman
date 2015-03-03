<?php
/**
 * @version     $Id: router.php 497 2012-12-21 15:08:22Z mrs.siam $
 * @package     JONGman
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
function JongmanBuildRoute( &$query )
{
   	$segments = array();

    if (isset($query['view']) && ($query['view'] != 'reservation') )
    {
    	$segments[] = $query['view'];
		/* We should use slug for schedule id here? */
    	if (isset($query['id'])) {
    		$segments[] = $query['id'];
    		unset($query['id']);
    	}
    	
    	if (isset($query['layout'])) {
    		$segments[] = $query['layout'];
    		unset($query['layout']);
    	}
    	
    	if (isset($query['sd'])) {
    		$segments[] = $query['sd'];
			unset($query['sd']);
    	}
        unset($query['view']);
    }
    return $segments;   
}

function JongmanParseRoute( &$segments )
{
    $vars	= array();
    $count	= count($segments);
	switch($segments[0]) {
		case 'schedule':
			$vars['view'] 	= 'schedule';
			$vars['id']		= (int)$segments[1];
			if (isset($segments[2])) {
				$vars['layout'] = $segments[2];
			}
			
			if (isset($segments[3])) {
				/** sd=2015-12-01 converted to 2015:12-01 **/
				$vars['sd'] = str_replace(':', '-', $segments[3]);
			}
			break;
	}
    
    return $vars;        
}

