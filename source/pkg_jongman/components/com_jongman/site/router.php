<?php
/**
 * @version     $Id: router.php 497 2012-12-21 15:08:22Z mrs.siam $
 * @package     JONGman
 * @copyright   Copyright (C) 2009 - 2011  Prasit Gebsaap. All rights reserved.
 * @license     GNU/GPL, see LICENSE.php
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;
function JongmanBuildRoute( $query )
{
    $segments = array();
    if (isset($query['view']) && ($query['view'] != 'reservation') )
    {
        /** first call alias is ok */
        unset($query['view']);
        unset($query['id']); 
    }
    
    return $segments;   
}

function JongmanParseRoute( &$segments )
{
    $vars = array();
    if (count($segments)==1) {
        list($name, $value) = split(':', $segments[0]);
        $vars['date']= $value;
        return $vars;
    }
    
    return $vars;        
}

