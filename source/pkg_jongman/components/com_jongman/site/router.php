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

    if (isset($query['view'])) {
    	$segments[] = $query['view'];
    	if ($query['view'] == 'schedule') {
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
    	}else if ($query['view'] == 'reservation') {
    		if (isset($query['id'])) {
    			$segments[] = $query['id'];
    			unset($query['id']);
    		}
    		
    		if (isset($query['layout'])) {
    			$segments[] = $query['layout'];
    			unset($query['layout']);
    		}
    		
    		if (isset($query['resource_id']))	{
    			$segments[] = $query['resource_id'];
    			unset($query['resource_id']);
    		}
    		
    		if (isset($query['schedule_id']))	{
    			$segments[] = $query['schedule_id'];
    			unset($query['schedule_id']);
    		}
    		
    		if (isset($query['start'])) {
    			$segments[] = $query['start'];
    			unset($query['start']);	
    		}
    		
    		if (isset($query['end'])) {
    			$segments[] = $query['end'];
    			unset($query['end']);	
    		}
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
		case 'reservation':
			
			$vars['view'] = 'reservation';
			if ($count == 3) {
				/** edit existing reservation **/
				$vars['id'] = $segments[1];
				$vars['layout'] = $segments[2];				
			}
			
			if ($count == 6) {
				$vars['layout'] 		= $segments[1];
				$vars['resource_id'] 	= $segments[2];
				$vars['schedule_id'] 	= $segments[3];
				
				list($sd, $st) = explode(' ',urldecode($segments[4]));
				$vars['start'] 	= str_replace(':', '-', $sd) . ' ' . str_replace('-', ':', $st) ;

				list($ed, $et) = explode(' ', urldecode($segments[5]));
				$vars['end']	=  str_replace(':', '-', $ed) . ' ' . str_replace('-', ':', $et) ;
			}
			break;
	}
    
    return $vars;        
}

